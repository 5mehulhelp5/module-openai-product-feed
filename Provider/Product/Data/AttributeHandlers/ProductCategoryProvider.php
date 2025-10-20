<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductCategoryProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly string $attributeCode
    ) {}

    /**
     * @throws NoSuchEntityException
     */
    public function provide(ProductInterface $product): string
    {
        $categoryIds = $product->getCategoryIds();

        return $this->categoryRepository->get(
            current($categoryIds) ?? 2,
            $product->getStoreId()
        )->getName();
    }
}
