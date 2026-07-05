<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Api\Data;

/**
 * Feed column definitions.
 *
 * Column names follow the OpenAI Product Feed file-upload specification (Stable):
 * https://developers.openai.com/commerce/specs/file-upload/products
 *
 * Note: `inventory_quantity` is not part of the current Stable schema and is
 * kept as a supplementary column for backwards compatibility.
 */
interface OpenAiProductHeadersInterface
{
    public const array HEADERS = [
        'item_id',
        'gtin',
        'mpn',
        'title',
        'description',
        'url',
        'is_eligible_search',
        'is_eligible_checkout',
        'brand',
        'product_category',
        'material',
        'weight',
        'item_weight_unit',
        'image_url',
        'price',
        'sale_price',
        'availability',
        'inventory_quantity',
        'is_digital',
        'group_id',
        'listing_has_variations',
        'variant_dict',
        'item_group_title',
        'related_product_id',
        'relationship_type',
        'seller_name',
        'seller_url',
        'seller_privacy_policy',
        'seller_tos',
        'return_policy',
        'return_deadline_in_days',
    ];
}
