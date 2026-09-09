<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Resolver\Inventory;

use Magento\Framework\App\ResourceConnection;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Batch resolver for MSI stock data.
 *
 * Replaces per-SKU GetProductSalableQty / isAvailable() calls (two or more
 * queries per product) with a single SELECT against the inventory_stock_{id}
 * view per collection page. On a 10k-row catalog this removes ~20k queries
 * per store view.
 *
 * The resolver is fail-open: when preloading is impossible (custom stock
 * setups, missing view), lookups return null and callers fall back to the
 * per-product Magento APIs.
 */
class StockDataResolver
{
    private const CHUNK_SIZE = 500;

    /** @var array<string, array{qty: float, salable: bool}> */
    private array $dataBySku = [];

    /** @var array<string, int> */
    private array $stockIdByWebsite = [];

    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly StockResolverInterface $stockResolver,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Preload stock data for a batch of SKUs in the current store's website scope.
     *
     * @param string[] $skus
     */
    public function preload(array $skus): void
    {
        $skus = array_values(array_unique(array_filter(array_map('strval', $skus))));
        $skus = array_diff($skus, array_keys($this->dataBySku));

        if (empty($skus)) {
            return;
        }

        try {
            $stockId = $this->resolveStockId();
            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName('inventory_stock_' . $stockId);

            foreach (array_chunk($skus, self::CHUNK_SIZE) as $chunk) {
                $rows = $connection->fetchAll(
                    $connection->select()
                        ->from($table, ['sku', 'quantity', 'is_salable'])
                        ->where('sku IN (?)', $chunk)
                );

                foreach ($rows as $row) {
                    $this->dataBySku[(string) $row['sku']] = [
                        'qty' => (float) $row['quantity'],
                        'salable' => (bool) $row['is_salable'],
                    ];
                }
            }
        } catch (\Throwable $exception) {
            // Fail open: callers fall back to per-product APIs.
            $this->logger->info(
                '[Angeo_OpenAiProductFeed] Batch stock preload unavailable, falling back to per-product lookups: '
                . $exception->getMessage()
            );
        }
    }

    public function getQuantity(string $sku): ?float
    {
        return $this->dataBySku[$sku]['qty'] ?? null;
    }

    public function isSalable(string $sku): ?bool
    {
        return $this->dataBySku[$sku]['salable'] ?? null;
    }

    /**
     * Reset per-store state. Stock scope is the website, so the map must not
     * leak between store views of different websites.
     */
    public function reset(): void
    {
        $this->dataBySku = [];
    }

    private function resolveStockId(): int
    {
        $websiteCode = (string) $this->storeManager->getWebsite()->getCode();

        if (!isset($this->stockIdByWebsite[$websiteCode])) {
            $this->stockIdByWebsite[$websiteCode] = (int) $this->stockResolver
                ->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)
                ->getStockId();
        }

        return $this->stockIdByWebsite[$websiteCode];
    }
}
