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
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Provides the full category path (`Parent > Child > Grandchild`) using the
 * `>` separator required by the OpenAI feed specification. Root categories
 * are excluded. Returns an empty string for uncategorized products.
 */
class ProductCategoryProvider implements ProductAttributeProviderInterface
{
    /**
     * Path depth at which visible categories start (skips root + store root).
     */
    private const PATH_OFFSET = 2;

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $categoryIds = $product->getCategoryIds();

        if (empty($categoryIds)) {
            return '';
        }

        $storeId = (int) $product->getStoreId();

        try {
            $category = $this->categoryRepository->get((int) current($categoryIds), $storeId);
        } catch (NoSuchEntityException) {
            return '';
        }

        $pathIds = array_slice($category->getPathIds(), self::PATH_OFFSET);
        $names = [];

        foreach ($pathIds as $pathId) {
            try {
                $name = (string) $this->categoryRepository->get((int) $pathId, $storeId)->getName();
            } catch (NoSuchEntityException) {
                continue;
            }

            if ($name !== '') {
                $names[] = $name;
            }
        }

        return implode(' > ', $names);
    }
}
