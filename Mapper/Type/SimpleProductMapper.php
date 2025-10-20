<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper\Type;

use Angeo\OpenAiProductFeed\Api\ProductMapperInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Angeo\OpenAiProductFeed\Model\Factory\OpenAiProductAttributeHandlerFactory;
use Angeo\OpenAiProductFeed\Provider\Product\ProductAttributesDataProvider;

class SimpleProductMapper implements ProductMapperInterface
{
    public function __construct(
        private readonly OpenAiProductAttributeHandlerFactory $handlerProvider,
        private readonly ProductAttributesDataProvider $attributesDataProvider
    ) {}

    public function map(ProductInterface $product): array
    {
        $data = [];

        foreach ($this->attributesDataProvider->provide() as $attribute) {
            $attributeDataProvider = $this->handlerProvider->create($attribute);
            $data[] = $attributeDataProvider->provide($product);
        }

        return $data;
    }
}
