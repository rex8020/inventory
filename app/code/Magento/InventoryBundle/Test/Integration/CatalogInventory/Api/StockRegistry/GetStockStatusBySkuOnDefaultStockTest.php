<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryBundle\Test\Integration\CatalogInventory\Api\StockRegistry;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GetStockStatusBySkuOnDefaultStockTest extends TestCase
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

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
        $this->defaultStockProvider = Bootstrap::getObjectManager()->get(DefaultStockProviderInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryBundle/Test/_files/default_stock_bundle_products.php
     *
     * @dataProvider getStockDataProvider
     * @param string $sku
     * @param int $status
     * @return void
     */
    public function testGetStatusIfScopeIdParameterIsNotPassed(string $sku, int $status): void
    {
        $stockStatus = $this->stockRegistry->getStockStatusBySku($sku);

        self::assertEquals($status, $stockStatus->getStockStatus());
        self::assertEquals(0, $stockStatus->getQty());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryBundle/Test/_files/default_stock_bundle_products.php
     *
     * @dataProvider getStockDataProvider
     * @param string $sku
     * @param int $status
     * @return void
     */
    public function testGetStatusIfScopeIdParameterIsPassed(string $sku, int $status): void
    {
        $stockStatus = $this->stockRegistry->getStockStatusBySku($sku, $this->defaultStockProvider->getId());

        self::assertEquals($status, $stockStatus->getStockStatus());
        self::assertEquals(0, $stockStatus->getQty());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryBundle/Test/_files/default_stock_bundle_products.php
     *
     * @dataProvider getStockDataProvider
     * @param string $sku
     * @return void
     */
    public function testGetStatusIfNotExistedScopeIdParameterIsPassed(string $sku): void
    {
        $notExistedScopeId = 100;
        $stockStatus = $this->stockRegistry->getStockStatusBySku($sku, $notExistedScopeId);

        self::assertEquals(0, $stockStatus->getStockStatus());
    }

    /**
     * @return array
     */
    public function getStockDataProvider(): array
    {
        return [
            ['bundle-product-in-stock', 1],
            ['bundle-product-out-of-stock', 0]
        ];
    }
}
