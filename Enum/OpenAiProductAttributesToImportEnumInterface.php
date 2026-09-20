<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Enum;

use Angeo\OpenAiProductFeed\Data\OpenAiProductAttributeData;

/**
 * Maps OpenAI feed column names (array keys) to the Magento attribute and
 * handler responsible for producing the value.
 */
interface OpenAiProductAttributesToImportEnumInterface
{
    /**
     * The handlers are virtualTypes declared in etc/di.xml. They exist only in
     * the object manager, so they are referenced by name, not by ::class.
     */
    public const HANDLER_NS = 'Angeo\\OpenAiProductFeed\\Provider\\Product\\Data\\AttributeHandlers\\';

    public const PRODUCT_ATTRIBUTES = [
        'item_id' => [
            OpenAiProductAttributeData::FIELD_NAME => 'sku',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'SkuProvider',
        ],
        'gtin' => [
            OpenAiProductAttributeData::FIELD_NAME => 'gtin',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'GtinProvider',
        ],
        'mpn' => [
            OpenAiProductAttributeData::FIELD_NAME => 'mpn',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'MpnProvider',
        ],
        'title' => [
            OpenAiProductAttributeData::FIELD_NAME => 'name',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'NameProvider',
        ],
        'description' => [
            OpenAiProductAttributeData::FIELD_NAME => 'description',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'DescriptionProvider',
        ],
        'url' => [
            OpenAiProductAttributeData::FIELD_NAME => 'url',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'UrlProvider',
        ],
        'is_eligible_search' => [
            OpenAiProductAttributeData::FIELD_NAME => 'enable_search',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'EnableSearchProvider',
        ],
        'is_eligible_checkout' => [
            OpenAiProductAttributeData::FIELD_NAME => 'enable_checkout',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'EnableCheckoutProvider',
        ],
        'brand' => [
            OpenAiProductAttributeData::FIELD_NAME => 'brand',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'BrandProvider',
        ],
        'product_category' => [
            OpenAiProductAttributeData::FIELD_NAME => 'product_category',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'CategoryProvider',
        ],
        'material' => [
            OpenAiProductAttributeData::FIELD_NAME => 'material',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'MaterialProvider',
        ],
        'weight' => [
            OpenAiProductAttributeData::FIELD_NAME => 'weight',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'WeightProvider',
        ],
        'item_weight_unit' => [
            OpenAiProductAttributeData::FIELD_NAME => 'weight',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'WeightUnitProvider',
        ],
        'image_url' => [
            OpenAiProductAttributeData::FIELD_NAME => 'image_url',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'ImageLinkProvider',
        ],
        'price' => [
            OpenAiProductAttributeData::FIELD_NAME => 'price',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'PriceProvider',
        ],
        'sale_price' => [
            OpenAiProductAttributeData::FIELD_NAME => 'special_price',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'SalePriceProvider',
        ],
        'availability' => [
            OpenAiProductAttributeData::FIELD_NAME => 'availability',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'AvailabilityProvider',
        ],
        'inventory_quantity' => [
            OpenAiProductAttributeData::FIELD_NAME => 'inventory_quantity',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => self::HANDLER_NS . 'InventoryQuantityProvider',
        ],
    ];
}
