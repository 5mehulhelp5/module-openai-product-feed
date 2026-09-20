<?php

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Test\Unit\Resolver;

use Angeo\OpenAiProductFeed\Resolver\Category\CategoryNameResolver;
use Angeo\OpenAiProductFeed\Resolver\Inventory\StockDataResolver;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ResolversTest extends TestCase
{
    // ── CategoryNameResolver ──────────────────────────────────────────────

    private function categoryResolver(): CategoryNameResolver
    {
        // Tree: 1(root)/2(store root)/10 Apparel /20 Shoes /30 Sneakers ; 40 Sale under store root
        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')->willReturn($this->categoryCollection([
            new FakeCategory(10, 'Apparel', '1/2/10'),
            new FakeCategory(20, 'Shoes', '1/2/10/20'),
            new FakeCategory(30, 'Sneakers', '1/2/10/20/30'),
            new FakeCategory(40, 'Sale', '1/2/40'),
            new FakeCategory(50, '', '1/2/50'), // nameless category
            new FakeCategory(60, 'Deep', '1/2/50/60'), // child of nameless
        ]));

        return new CategoryNameResolver($factory, $this->createMock(LoggerInterface::class));
    }

    /**
     * @param FakeCategory[] $items
     */
    private function categoryCollection(array $items): CategoryCollection
    {
        $collection = $this->createMock(CategoryCollection::class);
        $collection->method('setStoreId')->willReturnSelf();
        $collection->method('addAttributeToSelect')->willReturnSelf();
        $collection->method('getIterator')->willReturn(new \ArrayIterator($items));

        return $collection;
    }

    public function testFullPathIsBuiltWithSpecSeparator(): void
    {
        $this->assertSame('Apparel > Shoes > Sneakers', $this->categoryResolver()->getPath(30, 1));
    }

    public function testTopLevelCategoryHasNoSeparator(): void
    {
        $this->assertSame('Sale', $this->categoryResolver()->getPath(40, 1));
    }

    public function testRootCategoriesAreExcluded(): void
    {
        $path = $this->categoryResolver()->getPath(20, 1);
        $this->assertStringNotContainsString('1', $path);
        $this->assertSame('Apparel > Shoes', $path);
    }

    public function testUnknownCategoryReturnsEmptyString(): void
    {
        $this->assertSame('', $this->categoryResolver()->getPath(999, 1));
    }

    public function testNamelessAncestorsAreSkippedNotRendered(): void
    {
        $this->assertSame('Deep', $this->categoryResolver()->getPath(60, 1));
    }

    public function testCollectionLoadedOncePerStore(): void
    {
        $factory = $this->createMock(CollectionFactory::class);
        $factory->expects($this->once())->method('create')
            ->willReturn($this->categoryCollection([
                new FakeCategory(10, 'A', '1/2/10'),
            ]));

        $resolver = new CategoryNameResolver($factory, $this->createMock(LoggerInterface::class));
        $resolver->getPath(10, 1);
        $resolver->getPath(10, 1);
        $resolver->getPath(999, 1); // miss also must not re-query
    }

    // ── StockDataResolver ─────────────────────────────────────────────────

    private function stockResolverWith(array $rows, int &$queryCount = 0): StockDataResolver
    {
        $connection = $this->createMock(AdapterInterface::class);
        $requested = [];
        $connection->method('select')->willReturnCallback(function () use (&$requested) {
            $select = $this->createMock(Select::class);
            $select->method('from')->willReturnSelf();
            $select->method('where')->willReturnCallback(
                function (string $cond, $value) use ($select, &$requested) {
                    $requested = (array) $value;
                    return $select;
                }
            );
            return $select;
        });
        $connection->method('fetchAll')->willReturnCallback(
            function () use ($rows, &$queryCount, &$requested) {
                $queryCount++;
                return array_values(array_filter($rows, fn ($r) => in_array($r['sku'], $requested, true)));
            }
        );

        $resource = $this->createMock(ResourceConnection::class);
        $resource->method('getConnection')->willReturn($connection);
        $resource->method('getTableName')->willReturnArgument(0);

        $stock = $this->createMock(StockInterface::class);
        $stock->method('getStockId')->willReturn(1);
        $stockApi = $this->createMock(StockResolverInterface::class);
        $stockApi->method('execute')->willReturn($stock);

        $website = $this->createMock(WebsiteInterface::class);
        $website->method('getCode')->willReturn('base');
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getWebsite')->willReturn($website);

        return new StockDataResolver($resource, $stockApi, $storeManager, $this->createMock(LoggerInterface::class));
    }

    public function testPreloadedDataIsReturned(): void
    {
        $r = $this->stockResolverWith([
            ['sku' => 'A', 'quantity' => 7.0, 'is_salable' => 1],
            ['sku' => 'B', 'quantity' => 0.0, 'is_salable' => 0],
        ]);
        $r->preload(['A', 'B']);

        $this->assertSame(7.0, $r->getQuantity('A'));
        $this->assertTrue($r->isSalable('A'));
        $this->assertSame(0.0, $r->getQuantity('B'));
        $this->assertFalse($r->isSalable('B'));
    }

    public function testUnknownSkuReturnsNullForFallback(): void
    {
        $r = $this->stockResolverWith([]);
        $r->preload(['MISSING']);

        $this->assertNull($r->getQuantity('MISSING'));
        $this->assertNull($r->isSalable('MISSING'));
    }

    public function testAlreadyLoadedSkusAreNotRequeried(): void
    {
        $count = 0;
        $r = $this->stockResolverWith([['sku' => 'A', 'quantity' => 1.0, 'is_salable' => 1]], $count);
        $r->preload(['A']);
        $r->preload(['A']); // fully deduped -> no query
        $this->assertSame(1, $count);
    }

    public function testResetClearsSkuMap(): void
    {
        $r = $this->stockResolverWith([['sku' => 'A', 'quantity' => 1.0, 'is_salable' => 1]]);
        $r->preload(['A']);
        $r->reset();

        $this->assertNull($r->getQuantity('A'));
    }

    public function testPreloadFailureIsFailOpen(): void
    {
        $resource = $this->createMock(ResourceConnection::class);
        $resource->method('getConnection')->willThrowException(new \RuntimeException('no view'));

        $stockApi = $this->createMock(StockResolverInterface::class);
        $website = $this->createMock(WebsiteInterface::class);
        $website->method('getCode')->willReturn('base');
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getWebsite')->willReturn($website);

        $r = new StockDataResolver($resource, $stockApi, $storeManager, $this->createMock(LoggerInterface::class));
        $r->preload(['A']); // must not throw

        $this->assertNull($r->getQuantity('A'));
    }

    public function testLargeBatchesAreChunked(): void
    {
        $count = 0;
        $rows = [];
        $skus = [];
        for ($i = 1; $i <= 1200; $i++) {
            $skus[] = "SKU$i";
            $rows[] = ['sku' => "SKU$i", 'quantity' => 1.0, 'is_salable' => 1];
        }
        $r = $this->stockResolverWith($rows, $count);
        $r->preload($skus);

        $this->assertSame(3, $count); // 1200 / 500 -> 3 chunks
        $this->assertSame(1.0, $r->getQuantity('SKU1200'));
    }
}
