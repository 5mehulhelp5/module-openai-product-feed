<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;

class SelectProductAttributeProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly ProductResource $productResource,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $attribute = $this->productResource->getAttribute($this->attributeCode);

        if (!$attribute) {
            return '';
        }

        $value = $product->getData($this->attributeCode);
        $optionText = $attribute->getSource()->getOptionText($value);

        if (!is_array($optionText)) {
            return (string) $optionText;
        }

        return isset($optionText[$product->getStoreId()]) ?
            (string) $optionText[$product->getStoreId()] :
            (string) current($optionText);
    }
}
