<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers;

use Angeo\OpenAiProductFeed\Resolver\Category\CategoryNameResolver;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Provides the full category path (`Parent > Child > Grandchild`) using the
 * `>` separator required by the OpenAI feed specification. Category names are
 * served from a per-store in-memory map (one query per store view). Returns
 * an empty string for uncategorized products.
 */
class ProductCategoryProvider implements ProductAttributeProviderInterface
{
    public function __construct(
        private readonly CategoryNameResolver $categoryNameResolver,
        private readonly string $attributeCode
    ) {}

    public function provide(ProductInterface $product): string
    {
        $categoryIds = $product->getCategoryIds();

        if (empty($categoryIds)) {
            return '';
        }

        return $this->categoryNameResolver->getPath(
            (int) current($categoryIds),
            (int) $product->getStoreId()
        );
    }
}
