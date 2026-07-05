<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper\Type;

use Angeo\OpenAiProductFeed\Api\ProductMapperInterface;
use Angeo\OpenAiProductFeed\Provider\Product\ProductAttributeHandlerProvider;
use Angeo\OpenAiProductFeed\Provider\Product\ProductAttributesDataProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Psr\Log\LoggerInterface;

/**
 * Base implementation shared by all product type mappers.
 *
 * Builds a feed row keyed by feed column name via the attribute handler
 * pipeline. Individual handler failures are logged and result in an empty
 * cell instead of aborting the whole feed run.
 */
abstract class AbstractProductTypeMapper implements ProductMapperInterface
{
    public function __construct(
        protected readonly ProductAttributeHandlerProvider $handlerProvider,
        protected readonly ProductAttributesDataProvider $attributesDataProvider,
        protected readonly LoggerInterface $logger
    ) {}

    public function map(ProductInterface $product): array
    {
        return [$this->buildBaseRow($product)];
    }

    /**
     * @return array<string, string>
     */
    protected function buildBaseRow(ProductInterface $product): array
    {
        $row = [];

        foreach ($this->attributesDataProvider->provide() as $feedColumn => $attribute) {
            try {
                $handler = $this->handlerProvider->provide($attribute);
                $row[$feedColumn] = (string) $handler->provide($product);
            } catch (\Throwable $exception) {
                $this->logger->warning(
                    sprintf(
                        '[Angeo_OpenAiProductFeed] Could not resolve feed field "%s" for product SKU "%s": %s',
                        (string) $feedColumn,
                        (string) $product->getSku(),
                        $exception->getMessage()
                    ),
                    ['exception' => $exception]
                );
                $row[$feedColumn] = '';
            }
        }

        $row['is_digital'] = 'false';

        return $this->normalizeFlags($row);
    }

    /**
     * `is_eligible_checkout` requires `is_eligible_search=true` per spec.
     *
     * @param array<string, string> $row
     * @return array<string, string>
     */
    protected function normalizeFlags(array $row): array
    {
        if (($row['is_eligible_search'] ?? 'false') !== 'true') {
            $row['is_eligible_checkout'] = 'false';
        }

        return $row;
    }
}
