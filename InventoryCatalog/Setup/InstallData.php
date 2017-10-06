<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\InventoryCatalog\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\StockInterfaceFactory;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventoryApi\Api\AssignSourcesToStockInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\InventoryCatalog\Api\DefaultSourceProviderInterface;
use Magento\InventoryCatalog\Api\DefaultStockProviderInterface;

/**
 * Install Default Source, Stock and link them together
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var SourceInterfaceFactory
     */
    private $sourceFactory;

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    /**
     * @var StockInterfaceFactory
     */
    private $stockFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AssignSourcesToStockInterface
     */
    private $assignSourcesToStock;

    /**
     * @var DefaultSourceProviderInterface
     */
    private $defaultSourceResolver;

    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockResolver;

    /**
     * @param SourceRepositoryInterface $sourceRepository
     * @param SourceInterfaceFactory $sourceFactory
     * @param StockRepositoryInterface $stockRepository
     * @param StockInterfaceFactory $stockFactory
     * @param AssignSourcesToStockInterface $assignSourcesToStock
     * @param DataObjectHelper $dataObjectHelper
     * @param DefaultSourceProviderInterface $defaultSourceResolver
     * @param DefaultStockProviderInterface $defaultStockResolver
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SourceInterfaceFactory $sourceFactory,
        StockRepositoryInterface $stockRepository,
        StockInterfaceFactory $stockFactory,
        AssignSourcesToStockInterface $assignSourcesToStock,
        DataObjectHelper $dataObjectHelper,
        DefaultSourceProviderInterface $defaultSourceResolver,
        DefaultStockProviderInterface $defaultStockResolver
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->sourceFactory = $sourceFactory;
        $this->stockRepository = $stockRepository;
        $this->stockFactory = $stockFactory;
        $this->assignSourcesToStock = $assignSourcesToStock;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->defaultSourceResolver = $defaultSourceResolver;
        $this->defaultStockResolver = $defaultStockResolver;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addDefaultSource();
        $this->addDefaultStock();
        $this->assignStockToSource();
    }

    /**
     * Add default source
     *
     * @return void
     */
    private function addDefaultSource()
    {
        $data = [
            SourceInterface::SOURCE_ID => $this->defaultSourceResolver->getId(),
            SourceInterface::NAME => 'Default Source',
            SourceInterface::ENABLED => 1,
            SourceInterface::DESCRIPTION => 'Default Source',
            SourceInterface::LATITUDE => 0,
            SourceInterface::LONGITUDE => 0,
            SourceInterface::PRIORITY => 0,
            SourceInterface::COUNTRY_ID => 'US',
            SourceInterface::POSTCODE => '00000'
        ];
        $source = $this->sourceFactory->create();
        $this->dataObjectHelper->populateWithArray($source, $data, SourceInterface::class);
        $this->sourceRepository->save($source);
    }

    /**
     * Add default stock
     *
     * @return void
     */
    private function addDefaultStock()
    {
        $data = [
            StockInterface::STOCK_ID => $this->defaultStockResolver->getId(),
            StockInterface::NAME => 'Default Stock'
        ];
        $source = $this->stockFactory->create();
        $this->dataObjectHelper->populateWithArray($source, $data, StockInterface::class);
        $this->stockRepository->save($source);
    }

    /**
     * Assign default stock to default source
     *
     * @return void
     */
    private function assignStockToSource()
    {
        $this->assignSourcesToStock->execute(
            [$this->defaultSourceResolver->getId()],
            $this->defaultStockResolver->getId()
        );
    }
}
