<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\Data\ProductInterface;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;
use Angeo\OpenAiProductFeed\Formatter\ProductCurrencyFormatter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductPriceProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly ProductCurrencyFormatter $formatter,
        private readonly string $attributeCode
    ) {}

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function provide(ProductInterface $product): string
    {
        return $this->formatter->format(
            (float) $product->getData($this->attributeCode),
            (int) $product->getStoreId()
        );
    }
}
