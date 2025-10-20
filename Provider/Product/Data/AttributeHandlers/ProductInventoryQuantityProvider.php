<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;

class ProductInventoryQuantityProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly GetProductSalableQtyInterface $salableQty,
        private readonly StockResolverInterface $stockResolver,
        private readonly string $attributeCode
    ) {}

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws InputException
     */
    public function provide(ProductInterface $product): string
    {
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $stockDetails = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
        return (string) $this->salableQty->execute($product->getSku(), $stockDetails->getStockId());
    }
}
