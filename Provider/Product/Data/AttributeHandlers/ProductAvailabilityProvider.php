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

class ProductAvailabilityProvider implements ProductAttributeProviderInterface
{
    private const string STATUS_IN_STOCK = 'in_stock';
    private const string STATUS_OUT_OF_STOCK = 'out_of_stock';

    public function __construct(
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        return $product->isAvailable() ? self::STATUS_IN_STOCK : self::STATUS_OUT_OF_STOCK;
    }
}
