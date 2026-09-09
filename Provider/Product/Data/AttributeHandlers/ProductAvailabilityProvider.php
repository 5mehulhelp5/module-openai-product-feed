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
use Magento\Catalog\Model\Product;

/**
 * Provides the availability status from the batch-preloaded stock map,
 * falling back to Product::isAvailable() (which issues its own stock lookup)
 * only for SKUs that were not preloaded.
 */
class ProductAvailabilityProvider implements ProductAttributeProviderInterface
{
    private const STATUS_IN_STOCK = 'in_stock';
    private const STATUS_OUT_OF_STOCK = 'out_of_stock';

    public function __construct(
        private readonly StockDataResolver $stockDataResolver,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $salable = $this->stockDataResolver->isSalable((string) $product->getSku());

        if ($salable === null && $product instanceof Product) {
            $salable = $product->isAvailable();
        }

        return $salable ? self::STATUS_IN_STOCK : self::STATUS_OUT_OF_STOCK;
    }
}
