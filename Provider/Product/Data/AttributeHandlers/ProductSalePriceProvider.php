<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Angeo\OpenAiProductFeed\Formatter\ProductCurrencyFormatter;
use Angeo\OpenAiProductFeed\Resolver\ProductPriceResolver;

/**
 * Provides `sale_price`: the effective final price (catalog rules,
 * special price with date ranges, minimal price for composite types).
 * Emits an empty string when no active discount exists, per the spec rule
 * that sale_price must be less than or equal to price.
 */
class ProductSalePriceProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly ProductPriceResolver $priceResolver,
        private readonly ProductCurrencyFormatter $formatter,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $regular = $this->priceResolver->getRegularPrice($product);
        $final = $this->priceResolver->getFinalPrice($product);

        if ($final <= 0 || $regular <= 0 || $final >= $regular) {
            return '';
        }

        return $this->formatter->format($final, (int) $product->getStoreId());
    }
}
