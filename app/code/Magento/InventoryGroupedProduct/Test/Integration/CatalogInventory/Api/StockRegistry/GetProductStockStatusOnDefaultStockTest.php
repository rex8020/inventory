<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryGroupedProduct\Test\Integration\CatalogInventory\Api\StockRegistry;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryCatalogApi\Model\GetProductIdsBySkusInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GetProductStockStatusOnDefaultStockTest extends TestCase
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var GetProductIdsBySkusInterface
     */
    private $getProductIdsBySkus;

    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockProvider;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->stockRegistry = Bootstrap::getObjectManager()->get(StockRegistryInterface::class);
        $this->getProductIdsBySkus = Bootstrap::getObjectManager()->get(GetProductIdsBySkusInterface::class);
        $this->defaultStockProvider = Bootstrap::getObjectManager()->get(DefaultStockProviderInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryGroupedProduct/Test/_files/default_stock_grouped_products.php
     *
     * @dataProvider getStockDataProvider
     * @param string $sku
     * @param int $status
     * @return void
     */
    public function testGetStatusIfScopeIdParameterIsNotPassed(string $sku, int $status): void
    {
        $productId = $this->getProductIdsBySkus->execute([$sku])[$sku];
        $productStockStatus = $this->stockRegistry->getProductStockStatus($productId);

        self::assertEquals($status, $productStockStatus);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryGroupedProduct/Test/_files/default_stock_grouped_products.php
     *
     * @dataProvider getStockDataProvider
     * @param string $sku
     * @param int $status
     * @return void
     */
    public function testGetStatusIfScopeIdParameterIsPassed(string $sku, int $status): void
    {
        $productId = $this->getProductIdsBySkus->execute([$sku])[$sku];
        $productStockStatus = $this->stockRegistry->getProductStockStatus(
            $productId,
            $this->defaultStockProvider->getId()
        );

        self::assertEquals($status, $productStockStatus);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryGroupedProduct/Test/_files/default_stock_grouped_products.php
     *
     * @dataProvider getStockDataProvider
     * @param string $sku
     * @return void
     */
    public function testGetStatusIfNotExistedScopeIdParameterIsPassed(string $sku): void
    {
        $notExistedScopeId = 100;
        $productId = $this->getProductIdsBySkus->execute([$sku])[$sku];
        $productStockStatus = $this->stockRegistry->getProductStockStatus($productId, $notExistedScopeId);

        self::assertEquals(0, $productStockStatus);
    }

    /**
     * @return array
     */
    public function getStockDataProvider(): array
    {
        return [
            ['grouped_in_stock', 1],
            ['grouped_out_of_stock', 0]
        ];
    }
}
