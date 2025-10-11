<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProductMapperInterface
{
    public function map(ProductInterface $product): array;
}
