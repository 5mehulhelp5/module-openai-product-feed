<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product;

use Angeo\OpenAiProductFeed\Data\OpenAiProductAttributeData;
use Angeo\OpenAiProductFeed\Data\OpenAiProductAttributeDataFactory;
use Angeo\OpenAiProductFeed\Enum\OpenAiProductAttributesToImportEnumInterface;

class ProductAttributesDataProvider
{
    public function __construct(
        private readonly OpenAiProductAttributeDataFactory $attributeDataFactory
    ) {}

    public function provide(): array
    {
        return array_map(function ($config) {
            return $this->attributeDataFactory->create(
                [
                    'data' => [
                        OpenAiProductAttributeData::FIELD_NAME => $config[OpenAiProductAttributeData::FIELD_NAME],
                        OpenAiProductAttributeData::ATTRIBUTE_HANDLER => $config[OpenAiProductAttributeData::ATTRIBUTE_HANDLER],
                    ],
                ],
            );
        }, OpenAiProductAttributesToImportEnumInterface::PRODUCT_ATTRIBUTES);
    }
}
