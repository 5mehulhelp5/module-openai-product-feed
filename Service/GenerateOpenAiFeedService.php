<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Angeo\OpenAiProductFeed\Service\GenerateOpenAiFeedPerStoreService;

class GenerateOpenAiFeedService
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly GenerateOpenAiFeedPerStoreService $generateFeedForStore,
        private readonly Emulation $emulation
    ) {}

    /**
     * @throws LocalizedException
     */
    public function execute(): void
    {
        foreach ($this->storeManager->getStores() as $store) {
            $this->emulation->startEnvironmentEmulation($store->getId());

            $this->generateFeedForStore->execute($store);

            $this->emulation->stopEnvironmentEmulation();
        }
    }
}
