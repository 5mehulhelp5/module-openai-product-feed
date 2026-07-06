<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Renders a text attribute as plain text: strips HTML, decodes entities,
 * collapses whitespace and truncates to the spec limit (5,000 chars).
 */
class PlainTextProductAttributeProvider implements ProductAttributeProviderInterface
{
    private const MAX_LENGTH = 5000;

    public function __construct(
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $value = (string) $product->getData($this->attributeCode);

        if ($value === '') {
            return '';
        }

        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = trim((string) preg_replace('/\s+/u', ' ', $value));

        return mb_substr($value, 0, self::MAX_LENGTH);
    }
}
