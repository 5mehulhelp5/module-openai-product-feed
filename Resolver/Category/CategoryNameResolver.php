<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Resolver\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Resolves full category paths ("Parent > Child") from an in-memory map.
 *
 * All category names and path columns for a store view are loaded with a
 * single collection query on first use, replacing the previous
 * repository-per-ancestor lookups (hundreds of queries on large catalogs).
 */
class CategoryNameResolver
{
    /**
     * Path depth at which visible categories start (skips root + store root).
     */
    private const PATH_OFFSET = 2;

    /** @var array<int, array<int, array{name: string, path: string}>> [storeId => [categoryId => data]] */
    private array $mapByStore = [];

    public function __construct(
        private readonly CategoryCollectionFactory $categoryCollectionFactory,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Full category path for the given category in the given store,
     * e.g. "Apparel & Accessories > Shoes". Empty string when unknown.
     */
    public function getPath(int $categoryId, int $storeId): string
    {
        $map = $this->getMap($storeId);

        if (!isset($map[$categoryId])) {
            return '';
        }

        $pathIds = array_slice(explode('/', $map[$categoryId]['path']), self::PATH_OFFSET);
        $names = [];

        foreach ($pathIds as $pathId) {
            $name = $map[(int) $pathId]['name'] ?? '';

            if ($name !== '') {
                $names[] = $name;
            }
        }

        return implode(' > ', $names);
    }

    /**
     * @return array<int, array{name: string, path: string}>
     */
    private function getMap(int $storeId): array
    {
        if (!isset($this->mapByStore[$storeId])) {
            $this->mapByStore[$storeId] = [];

            try {
                $collection = $this->categoryCollectionFactory->create();
                $collection->setStoreId($storeId)
                    ->addAttributeToSelect('name');

                foreach ($collection as $category) {
                    $this->mapByStore[$storeId][(int) $category->getId()] = [
                        'name' => (string) $category->getName(),
                        'path' => (string) $category->getPath(),
                    ];
                }
            } catch (\Throwable $exception) {
                $this->logger->warning(
                    '[Angeo_OpenAiProductFeed] Could not preload category names for store '
                    . $storeId . ': ' . $exception->getMessage()
                );
            }
        }

        return $this->mapByStore[$storeId];
    }
}
