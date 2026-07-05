<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Provides the salable quantity. Composite product types (configurable,
 * bundle, grouped) have no salable quantity of their own; for these an
 * empty string is returned instead of failing the row.
 */
class ProductInventoryQuantityProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly GetProductSalableQtyInterface $salableQty,
        private readonly StockResolverInterface $stockResolver,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        try {
            $websiteCode = $this->storeManager->getWebsite()->getCode();
            $stockDetails = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);

            return (string) $this->salableQty->execute($product->getSku(), $stockDetails->getStockId());
        } catch (\Throwable) {
            return '';
        }
    }
}
