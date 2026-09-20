<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\SaleableInterface;

/**
 * Resolves regular and final prices consistently across all product types.
 *
 * For composite types (configurable, bundle, grouped) the resolver falls
 * back to the minimal price, so the feed always exposes the "from" price.
 */
class ProductPriceResolver
{
    private const PRICE_REGULAR = 'regular_price';
    private const PRICE_FINAL = 'final_price';

    public function getRegularPrice(ProductInterface $product): float
    {
        $regular = $this->extract($product, self::PRICE_REGULAR);

        return $regular > 0 ? $regular : $this->extract($product, self::PRICE_FINAL);
    }

    public function getFinalPrice(ProductInterface $product): float
    {
        return $this->extract($product, self::PRICE_FINAL);
    }

    private function extract(ProductInterface $product, string $priceCode): float
    {
        if (!$product instanceof SaleableInterface) {
            return $this->rawPrice($product);
        }

        try {
            $price = $product->getPriceInfo()->getPrice($priceCode);
            $value = (float) ($price->getAmount()->getValue() ?? 0.0);

            if ($value <= 0 && method_exists($price, 'getMinimalPrice')) {
                $value = (float) ($price->getMinimalPrice()->getValue() ?? 0.0);
            }

            return $value;
        } catch (\Throwable) {
            return $this->rawPrice($product);
        }
    }

    /** The stored price attribute, without price-model calculation. */
    private function rawPrice(ProductInterface $product): float
    {
        return $product instanceof DataObject
            ? (float) $product->getData('price')
            : (float) $product->getPrice();
    }
}
