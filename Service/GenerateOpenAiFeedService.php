<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Service;

use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Generates the feed for all (or selected) store views. A failure in one
 * store view is logged and does not prevent the remaining store views from
 * being exported.
 */
class GenerateOpenAiFeedService
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly GenerateOpenAiFeedPerStoreService $generateFeedForStore,
        private readonly Emulation $emulation,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @param string[] $storeCodes Limit generation to these store codes (empty = all stores)
     * @param callable|null $progress Called with (string $storeCode, int $rowsSoFar, int $totalProducts)
     */
    public function execute(array $storeCodes = [], ?callable $progress = null): void
    {
        foreach ($this->storeManager->getStores() as $store) {
            if (!empty($storeCodes) && !in_array((string) $store->getCode(), $storeCodes, true)) {
                continue;
            }

            $this->emulation->startEnvironmentEmulation($store->getId());

            try {
                $storeProgress = $progress === null
                    ? null
                    : static fn (int $rows, int $total) => $progress((string) $store->getCode(), $rows, $total);

                $this->generateFeedForStore->execute($store, $storeProgress);
            } catch (\Throwable $exception) {
                $this->logger->error(
                    sprintf(
                        '[Angeo_OpenAiProductFeed] Unexpected error while generating the feed for store ID %d, the store was skipped: %s',
                        (int) $store->getId(),
                        $exception->getMessage()
                    ),
                    ['exception' => $exception]
                );
            } finally {
                $this->emulation->stopEnvironmentEmulation();
            }
        }
    }
}
