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

class BasicProductAttributeProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        return (string) $product->getData($this->attributeCode);
    }
}
