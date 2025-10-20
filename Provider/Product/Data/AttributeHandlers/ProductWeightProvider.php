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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Helper\Data;

class ProductWeightProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        return $product->getData($this->attributeCode) ?? 0 . ' ' . $this->getWeightUnit();
    }

    private function getWeightUnit(): string
    {
        return $this->scopeConfig->getValue(Data::XML_PATH_WEIGHT_UNIT);
    }
}
