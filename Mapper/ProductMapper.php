<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Angeo\OpenAiProductFeed\Api\ProductMapperInterface;
use Angeo\OpenAiProductFeed\Service\EnrichmentDataService;

class ProductMapper implements ProductMapperInterface
{
    public function __construct(
        private readonly EnrichmentDataService $service,
        private readonly array $mappers = []
    ) {}

    public function map(ProductInterface $product): array
    {
        return array_merge(
            $this->getProductTypeMapper($product)->map($product),
            ...$this->service->execute()
        );
    }

    private function getProductTypeMapper(ProductInterface $product): ProductMapperInterface
    {
        $type = $product->getTypeId();

        if (isset($this->mappers[$type])) {
            return $this->mappers[$type];
        }

        return $this->mappers[Type::TYPE_SIMPLE];
    }
}
