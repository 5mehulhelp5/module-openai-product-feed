<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;

class ProductCollectionProvider
{
    private const int BATCH_SIZE = 100;

    public function __construct(
        private readonly ProductCollectionFactory $productCollectionFactory
    ) {}

    /**
     * @throws LocalizedException
     */
    public function provide(int $page, ?int $storeId = null): ProductCollection
    {
        $collection = $this->productCollectionFactory->create();

        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED)
            ->addMediaGalleryData()
            ->addAttributeToFilter(ProductInterface::VISIBILITY, [
                'in' => [
                    Visibility::VISIBILITY_IN_CATALOG,
                    Visibility::VISIBILITY_IN_SEARCH,
                    Visibility::VISIBILITY_BOTH,
                ]
            ])
            ->addStoreFilter($storeId)
            ->setPageSize(self::BATCH_SIZE)
            ->setCurPage($page);

        return $collection;
    }
}
