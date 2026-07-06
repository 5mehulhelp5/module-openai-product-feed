<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Provides the `item_weight_unit` feed field. Emits a spec-friendly
 * abbreviation (lb / kg) and only when the product actually has a weight.
 */
class ProductWeightUnitProvider implements ProductAttributeProviderInterface
{
    private const UNIT_MAP = [
        'lbs' => 'lb',
        'kgs' => 'kg',
    ];

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $weight = $product->getData('weight');

        if ($weight === null || $weight === '' || (float) $weight <= 0) {
            return '';
        }

        $unit = (string) $this->scopeConfig->getValue(
            Data::XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $product->getStoreId()
        );

        return self::UNIT_MAP[$unit] ?? $unit;
    }
}
