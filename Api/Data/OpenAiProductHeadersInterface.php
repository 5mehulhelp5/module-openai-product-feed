<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Api\Data;

interface OpenAiProductHeadersInterface
{
    public const array HEADERS = [
        'id',
        'title',
        'description',
        'enable_search',
        'enable_checkout',
        'gtin',
        'mpn',
        'url',
        'product_category',
        'brand',
        'material',
        'weight',
        'image_link',
        'price',
        'sale_price',
        'availability',
        'inventory_quantity',
        'seller_name',
        'seller_url',
        'seller_privacy_policy',
        'seller_tos',
        'return_policy',
        'return_window',
    ];
}
