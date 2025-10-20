<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Enum;

use Angeo\OpenAiProductFeed\Data\OpenAiProductAttributeData;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\SkuProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\NameProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\EnableSearchProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\EnableCheckoutProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\GtinProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\MpnProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\DescriptionProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\PriceProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\SpecialPriceProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\MaterialProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\BrandProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\WeightProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ImageLinkProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\CategoryProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\UrlProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\AvailabilityProvider;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\InventoryQuantityProvider;

interface OpenAiProductAttributesToImportEnumInterface
{
    public const array PRODUCT_ATTRIBUTES = [
        'id' => [
            OpenAiProductAttributeData::FIELD_NAME => 'sku',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => SkuProvider::class,
        ],
        'title' => [
            OpenAiProductAttributeData::FIELD_NAME => 'name',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => NameProvider::class,
        ],
        'description' => [
            OpenAiProductAttributeData::FIELD_NAME => 'description',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => DescriptionProvider::class,
        ],
        'enable_search' => [
            OpenAiProductAttributeData::FIELD_NAME => 'enable_search',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => EnableSearchProvider::class,
        ],
        'enable_checkout' => [
            OpenAiProductAttributeData::FIELD_NAME => 'enable_checkout',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => EnableCheckoutProvider::class,
        ],
        'gtin' => [
            OpenAiProductAttributeData::FIELD_NAME => 'gtin',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => GtinProvider::class,
        ],
        'mpn' => [
            OpenAiProductAttributeData::FIELD_NAME => 'mpn',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => MpnProvider::class,
        ],
        'url' => [
            OpenAiProductAttributeData::FIELD_NAME => 'url',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => UrlProvider::class,
        ],
        'product_category' => [
            OpenAiProductAttributeData::FIELD_NAME => 'product_category',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => CategoryProvider::class,
        ],
        'brand' => [
            OpenAiProductAttributeData::FIELD_NAME => 'brand',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => BrandProvider::class,
        ],
        'material' => [
            OpenAiProductAttributeData::FIELD_NAME => 'material',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => MaterialProvider::class,
        ],
        'weight' => [
            OpenAiProductAttributeData::FIELD_NAME => 'weight',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => WeightProvider::class,
        ],
        'image_link' => [
            OpenAiProductAttributeData::FIELD_NAME => 'image_link',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => ImageLinkProvider::class,
        ],
        'price' => [
            OpenAiProductAttributeData::FIELD_NAME => 'price',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => PriceProvider::class,
        ],
        'special_price' => [
            OpenAiProductAttributeData::FIELD_NAME => 'special_price',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => SpecialPriceProvider::class,
        ],
        'availability' => [
            OpenAiProductAttributeData::FIELD_NAME => 'availability',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => AvailabilityProvider::class,
        ],
        'inventory_quantity' => [
            OpenAiProductAttributeData::FIELD_NAME => 'inventory_quantity',
            OpenAiProductAttributeData::ATTRIBUTE_HANDLER => InventoryQuantityProvider::class,
        ],
    ];
}
