<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Angeo\OpenAiProductFeed\Resolver\Inventory\StockDataResolver;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Provides the salable quantity from the batch-preloaded stock map, falling
 * back to the per-SKU MSI API when the SKU was not preloaded. Composite
 * product types (configurable, bundle, grouped) have no salable quantity of
 * their own; for these an empty string is returned instead of failing the row.
 */
class ProductInventoryQuantityProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly StockDataResolver $stockDataResolver,
        private readonly StoreManagerInterface $storeManager,
        private readonly GetProductSalableQtyInterface $salableQty,
        private readonly StockResolverInterface $stockResolver,
        private readonly string $attributeCode
    ) {}

    /**
     * Product types that have no salable quantity of their own. The stock
     * view exposes qty=0 rows for these under the default stock, which would
     * wrongly export "0" on listing rows; they must stay empty.
     */
    private const COMPOSITE_TYPES = ['configurable', 'bundle', 'grouped'];

    public function provide(ProductInterface $product): string
    {
        if (in_array((string) $product->getTypeId(), self::COMPOSITE_TYPES, true)) {
            return '';
        }

        $sku = (string) $product->getSku();

        $qty = $this->stockDataResolver->getQuantity($sku);
        if ($qty !== null) {
            return (string) $qty;
        }

        try {
            $websiteCode = $this->storeManager->getWebsite()->getCode();
            $stockDetails = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);

            return (string) $this->salableQty->execute($sku, $stockDetails->getStockId());
        } catch (\Throwable) {
            return '';
        }
    }
}
