<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
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
    private const BATCH_SIZE = 100;

    /**
     * Attributes required by the feed pipeline. Loading an explicit list
     * (instead of '*') and skipping the media gallery join reduces both the
     * collection query cost and the hydration cost per page. Extend the list
     * via the `attributes` di.xml argument when custom handlers need more.
     */
    private const DEFAULT_ATTRIBUTES = [
        'name',
        'description',
        'gtin',
        'mpn',
        'enable_search',
        'enable_checkout',
        'brand',
        'material',
        'weight',
        'image',
        'price',
        'special_price',
        'special_from_date',
        'special_to_date',
        'tax_class_id',
        'url_key',
    ];

    /**
     * @param string[] $attributes
     */
    public function __construct(
        private readonly ProductCollectionFactory $productCollectionFactory,
        private readonly array $attributes = []
    ) {}

    /**
     * @throws LocalizedException
     */
    public function provide(int $page, ?int $storeId = null): ProductCollection
    {
        $collection = $this->productCollectionFactory->create();

        $collection->addAttributeToSelect(array_merge(self::DEFAULT_ATTRIBUTES, $this->attributes))
            ->addAttributeToFilter(ProductInterface::STATUS, ['eq' => Status::STATUS_ENABLED])
            ->addAttributeToFilter(ProductInterface::VISIBILITY, [
                'in' => [
                    Visibility::VISIBILITY_IN_CATALOG,
                    Visibility::VISIBILITY_IN_SEARCH,
                    Visibility::VISIBILITY_BOTH,
                ]
            ])
            ->addCategoryIds()
            ->addUrlRewrite()
            ->addStoreFilter($storeId)
            ->setPageSize(self::BATCH_SIZE)
            ->setCurPage($page);

        return $collection;
    }
}
