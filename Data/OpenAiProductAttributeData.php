<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Data;

use Magento\Framework\DataObject;

class OpenAiProductAttributeData extends DataObject
{
    public const string FIELD_NAME = 'field_name';
    public const string ATTRIBUTE_HANDLER = 'attribute_handler';

    public function getFieldName(): ?string
    {
        $value = $this->getData(self::FIELD_NAME);
        return $value === null ? null : (string) $value;
    }

    public function setFieldName(?string $fieldName): void
    {
        $this->setData(self::FIELD_NAME, $fieldName);
    }

    public function getProductAttributeHandler(): ?string
    {
        return $this->getData(self::ATTRIBUTE_HANDLER);
    }

    public function setProductAttributeHandler(?string $attributeHandler): void
    {
        $this->setData(self::ATTRIBUTE_HANDLER, $attributeHandler);
    }
}
