<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */


declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Api\Data;

interface OpenAiProductAttributesInterface
{
    public const OPENAI_ENABLE_SEARCH_ATTRIBUTE = 'enable_search';
    public const OPENAI_ENABLE_CHECKOUT_ATTRIBUTE = 'enable_checkout';
}
