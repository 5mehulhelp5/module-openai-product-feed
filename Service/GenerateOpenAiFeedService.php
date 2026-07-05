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
 * Generates the feed for all store views. A failure in one store view is
 * logged and does not prevent the remaining store views from being exported.
 */
class GenerateOpenAiFeedService
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly GenerateOpenAiFeedPerStoreService $generateFeedForStore,
        private readonly Emulation $emulation,
        private readonly LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        foreach ($this->storeManager->getStores() as $store) {
            $this->emulation->startEnvironmentEmulation($store->getId());

            try {
                $this->generateFeedForStore->execute($store);
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
