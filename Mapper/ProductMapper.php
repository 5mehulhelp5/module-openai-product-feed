<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper;

use Angeo\OpenAiProductFeed\Api\ProductMapperInterface;
use Angeo\OpenAiProductFeed\Service\EnrichmentDataService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Psr\Log\LoggerInterface;

/**
 * Composite mapper: dispatches the product to its type-specific mapper and
 * merges store-level enrichment data (seller, return policy) into each row.
 */
class ProductMapper implements ProductMapperInterface
{
    /**
     * @param array<string, ProductMapperInterface> $mappers Type mappers keyed by product type id
     */
    public function __construct(
        private readonly EnrichmentDataService $service,
        private readonly LoggerInterface $logger,
        private readonly array $mappers = []
    ) {}

    public function map(ProductInterface $product): array
    {
        $enrichment = array_merge([], ...$this->service->execute());

        $rows = [];

        foreach ($this->getProductTypeMapper($product)->map($product) as $row) {
            $rows[] = array_merge($row, $enrichment);
        }

        return $rows;
    }

    private function getProductTypeMapper(ProductInterface $product): ProductMapperInterface
    {
        $type = (string) $product->getTypeId();

        if (isset($this->mappers[$type])) {
            return $this->mappers[$type];
        }

        $this->logger->info(
            sprintf(
                '[Angeo_OpenAiProductFeed] No dedicated mapper for product type "%s" (SKU "%s"), falling back to the simple product mapper.',
                $type,
                (string) $product->getSku()
            )
        );

        return $this->mappers[Type::TYPE_SIMPLE];
    }
}
