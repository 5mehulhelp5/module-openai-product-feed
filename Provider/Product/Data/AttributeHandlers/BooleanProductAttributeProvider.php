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
 * Renders a boolean product attribute as a lower-case `true` / `false` string,
 * as required by the OpenAI feed specification.
 */
class BooleanProductAttributeProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        return $product->getData($this->attributeCode) ? 'true' : 'false';
    }
}
