<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper\Type;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Maps virtual and downloadable products: flagged as digital goods with
 * no physical weight, per the OpenAI feed specification (`is_digital`).
 */
class VirtualProductMapper extends AbstractProductTypeMapper
{
    public function map(ProductInterface $product): array
    {
        $row = $this->buildBaseRow($product);

        $row['is_digital'] = 'true';
        $row['weight'] = '';
        $row['item_weight_unit'] = '';

        return [$row];
    }
}
