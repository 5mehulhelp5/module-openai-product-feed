<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Service;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Angeo\OpenAiProductFeed\Provider\Product\ProductCollectionProvider;
use Angeo\OpenAiProductFeed\Writer\CsvFileWriterProvider;
use Angeo\OpenAiProductFeed\Api\ProductMapperInterface;
use Angeo\OpenAiProductFeed\Exception\GenerateOpenAiFeedForStoreException;

class GenerateOpenAiFeedPerStoreService
{
    public function __construct(
        private readonly ProductCollectionProvider $productsCollectionProvider,
        private readonly CsvFileWriterProvider $csvFileWriterProvider,
        private readonly ProductMapperInterface $productMapper
    ) {}

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(StoreInterface $store): void
    {
        $storeId = (int) $store->getId();

        try {
            $fileWriter = $this->csvFileWriterProvider->provide($store);
        } catch (NoSuchEntityException $exception) {
            throw new GenerateOpenAiFeedForStoreException(
                __('The writer cann\'t be created for the store ID: %1', $storeId),
                $exception
            );
        }

        $currentPage = 1;
        $rows = [];

        do {
            $collection = $this->productsCollectionProvider->provide(
                $currentPage,
                $storeId
            );

            /** @var Product[] $items */
            $items = $collection->getItems();

            foreach ($items as $product) {
                if (isset($rows[$product->getId()])) {
                    continue;
                }

                try {
                    $rows[$product->getId()] = $this->productMapper->map($product);
                } catch (LocalizedException $exception) {
                    throw new GenerateFeedForStoreException(
                        __(
                            'Product can not be mapped to feed row. Product ID: %1 . Error: %2',
                            $product->getId(),
                            $exception->getMessage()
                        ),
                        $exception
                    );
                }
            }

            $currentPage++;
        } while ($this->canProceed($collection, $currentPage));

        $fileWriter->write($rows);
    }

    private function canProceed(ProductCollection $productCollection, int $currentPage): bool
    {
        $pageSize = $productCollection->getPageSize();
        return $pageSize * $currentPage < $productCollection->getSize() + $pageSize;
    }
}
