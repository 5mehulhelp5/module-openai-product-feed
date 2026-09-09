<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Service;

use Angeo\OpenAiProductFeed\Api\ProductMapperInterface;
use Angeo\OpenAiProductFeed\Provider\Product\ProductCollectionProvider;
use Angeo\OpenAiProductFeed\Resolver\Inventory\StockDataResolver;
use Angeo\OpenAiProductFeed\Writer\CsvFileWriterProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Store\Api\Data\StoreInterface;
use Psr\Log\LoggerInterface;

/**
 * Generates the feed file for a single store view.
 *
 * Failures are logged and never abort the run: a failing product is skipped
 * so the remaining catalog is still exported, and a failing writer skips
 * only the affected store.
 *
 * Stock data for every collection page is batch-preloaded before mapping,
 * so availability and salable quantity are resolved from memory instead of
 * per-SKU queries.
 */
class GenerateOpenAiFeedPerStoreService
{
    public function __construct(
        private readonly ProductCollectionProvider $productsCollectionProvider,
        private readonly CsvFileWriterProvider $csvFileWriterProvider,
        private readonly ProductMapperInterface $productMapper,
        private readonly StockDataResolver $stockDataResolver,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @param callable|null $progress Called after each page with (int $rowsSoFar, int $totalProducts)
     */
    public function execute(StoreInterface $store, ?callable $progress = null): void
    {
        $storeId = (int) $store->getId();

        try {
            $fileWriter = $this->csvFileWriterProvider->provide($store);
        } catch (\Throwable $exception) {
            $this->logger->error(
                sprintf(
                    '[Angeo_OpenAiProductFeed] The feed writer could not be created for store ID %d, the store was skipped: %s',
                    $storeId,
                    $exception->getMessage()
                ),
                ['exception' => $exception]
            );

            return;
        }

        // Stock scope is the website: never leak data between store views.
        $this->stockDataResolver->reset();

        $currentPage = 1;
        $rows = [];
        $skipped = 0;

        do {
            $collection = $this->productsCollectionProvider->provide(
                $currentPage,
                $storeId
            );

            $items = $collection->getItems();

            $this->stockDataResolver->preload(
                array_map(static fn ($product) => (string) $product->getSku(), $items)
            );

            foreach ($items as $product) {
                try {
                    $mappedRows = $this->productMapper->map($product);
                } catch (\Throwable $exception) {
                    $skipped++;
                    $this->logger->error(
                        sprintf(
                            '[Angeo_OpenAiProductFeed] Product could not be mapped to a feed row and was skipped. Product ID: %d, SKU: %s. Error: %s',
                            (int) $product->getId(),
                            (string) $product->getSku(),
                            $exception->getMessage()
                        ),
                        ['exception' => $exception]
                    );
                    continue;
                }

                foreach ($mappedRows as $row) {
                    $itemId = (string) ($row['item_id'] ?? '');

                    if ($itemId === '' || isset($rows[$itemId])) {
                        continue;
                    }

                    $rows[$itemId] = $row;
                }
            }

            if ($progress !== null) {
                $progress(count($rows), (int) $collection->getSize());
            }

            $currentPage++;
        } while ($this->canProceed($collection, $currentPage));

        try {
            $fileWriter->write($rows);
        } catch (\Throwable $exception) {
            $this->logger->error(
                sprintf(
                    '[Angeo_OpenAiProductFeed] The feed file could not be written for store ID %d: %s',
                    $storeId,
                    $exception->getMessage()
                ),
                ['exception' => $exception]
            );

            return;
        }

        $this->logger->info(
            sprintf(
                '[Angeo_OpenAiProductFeed] Feed generated for store ID %d: %d rows written, %d products skipped.',
                $storeId,
                count($rows),
                $skipped
            )
        );
    }

    private function canProceed(ProductCollection $productCollection, int $currentPage): bool
    {
        $pageSize = $productCollection->getPageSize();

        return $pageSize * $currentPage < $productCollection->getSize() + $pageSize;
    }
}
