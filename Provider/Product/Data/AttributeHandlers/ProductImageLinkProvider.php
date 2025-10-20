<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\Escaper;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;

class ProductImageLinkProvider implements ProductAttributeProviderInterface
{
    private const IMAGE_TYPE = 'product_page_image_large';

    public function __construct(
        private readonly UrlBuilder $imageUrlBuilder,
        private readonly Escaper $escaper,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        if ($product->getImage() === null) {
            return '';
        }

        $imageUrl = $this->imageUrlBuilder->getUrl($product->getImage(), self::IMAGE_TYPE);
        return $this->escaper->escapeUrl($imageUrl);
    }
}
