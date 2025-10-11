<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;
use Magento\Framework\Escaper;

class ProductUrlProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly Escaper $escaper,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        return $this->escaper->escapeUrl($product->getUrlModel()->getUrlInStore($product));
    }
}
