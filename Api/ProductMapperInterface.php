<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProductMapperInterface
{
    /**
     * Maps a product to one or more feed rows.
     *
     * A single catalog product may produce multiple feed rows, e.g. a
     * configurable product is exported as a parent listing plus one row
     * per child variant.
     *
     * @return array<int, array<string, string>> List of rows keyed by feed column name
     */
    public function map(ProductInterface $product): array;
}
