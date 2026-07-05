<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Provides the numeric `weight` feed field. The unit is exported separately
 * via `item_weight_unit`, per the OpenAI feed specification.
 */
class ProductWeightProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $weight = $product->getData($this->attributeCode);

        if ($weight === null || $weight === '' || (float) $weight <= 0) {
            return '';
        }

        return rtrim(rtrim(number_format((float) $weight, 4, '.', ''), '0'), '.');
    }
}
