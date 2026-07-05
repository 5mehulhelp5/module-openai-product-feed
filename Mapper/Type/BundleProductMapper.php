<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper\Type;

/**
 * Maps bundle products. Pricing resolves through the price-info pipeline,
 * so dynamic bundles export their minimal ("from") price and fixed bundles
 * export the fixed price.
 */
class BundleProductMapper extends AbstractProductTypeMapper
{
}
