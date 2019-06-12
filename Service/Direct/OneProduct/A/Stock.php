<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Direct\OneProduct\A;

use Flancer32\DemoImport\Config as Cfg;
use Magento\CatalogInventory\Api\Data\StockStatusInterface as EStockStatus;

/**
 * Locally used (in 'Flancer32\DemoImport\Service\Direct\OneProduct' only) helper to create product's stock data.
 */
class Stock
{
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Save product's stock data into DB (using old structure & new MSI structure).
     *
     * @param int $prodId is used as product identifier in old inventory structure
     * @param string $sku is used as product identifier in new inventory structure (MSI)
     * @param float $qty
     */
    public function create($prodId, $sku, $qty)
    {
        $isInStock = ($qty > 0);
        $source = Cfg::INV_NEW_STOCK_CODE_DEFAULT;

        /* save inventory data into old structure */
        $this->createOldItem($prodId, $qty);
        $this->createOldStatus($prodId, $qty);

        /* save inventory data into new structure (MSI) */
        $this->createNewItem($sku, $qty);
    }

    private function createNewItem($sku, $qty)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $tblName = Cfg::TBL_INVENTORY_SOURCE_ITEM;
        $table = $this->resource->getTableName($tblName);
        $status = \Magento\InventoryApi\Api\Data\SourceItemInterface::STATUS_IN_STOCK;
        $bind = [
            Cfg::T_INV_SOURCE_ITEM_F_SOURCE_CODE => Cfg::INV_NEW_STOCK_CODE_DEFAULT,
            Cfg::T_INV_SOURCE_ITEM_F_SKU => $sku,
            Cfg::T_INV_SOURCE_ITEM_F_QUANTITY => $qty,
            Cfg::T_INV_SOURCE_ITEM_F_STATUS => $status
        ];
        $conn->insert($table, $bind);
    }

    /**
     * Create new record in `cataloginventory_stock_item` table.
     *
     * @param int $prodId
     * @param float $qty
     */
    private function createOldItem($prodId, $qty)
    {
        $isQtyDecimal = (((int)$qty) != $qty);
        $isInStock = ($qty > 0);
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::TBL_CATALOGINVENTORY_STOCK_ITEM);
        $bind = [
            Cfg::T_CATINV_STOCK_ITEM_F_PRODUCT_ID => $prodId,
            Cfg::T_CATINV_STOCK_ITEM_F_STOCK_ID => Cfg::INV_OLD_STOCK_ID_DEFAULT,
            Cfg::T_CATINV_STOCK_ITEM_F_QTY => $qty,
            Cfg::T_CATINV_STOCK_ITEM_F_QTY_INCREMENTS => 1,
            Cfg::T_CATINV_STOCK_ITEM_F_NOTIFY_STOCK_QTY => 1,
            Cfg::T_CATINV_STOCK_ITEM_F_IS_QTY_DECIMAL => $isQtyDecimal,
            Cfg::T_CATINV_STOCK_ITEM_F_IS_IN_STOCK => $isInStock,
            Cfg::T_CATINV_STOCK_ITEM_F_MANAGE_STOCK => true,
            Cfg::T_CATINV_STOCK_ITEM_F_WEBSITE_ID => Cfg::WEBSITE_ID_ADMIN
        ];
        $conn->insert($table, $bind);
    }


    /**
     * Create new record in `cataloginventory_stock_status` table.
     *
     * @param int $prodId
     * @param float $qty
     */
    private function createOldStatus($prodId, $qty)
    {
        $isInStock = ($qty > 0);
        $status = EStockStatus::STATUS_IN_STOCK;
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $tblName = \Flancer32\DemoImport\Config::TBL_CATALOGINVENTORY_STOCK_STATUS;
        $table = $this->resource->getTableName($tblName);
        $bind = [
            /* item_id */
            Cfg::T_CATINV_STOCK_STATUS_F_PRODUCT_ID => $prodId,
            Cfg::T_CATINV_STOCK_STATUS_F_WEBSITE_ID => Cfg::WEBSITE_ID_ADMIN,
            Cfg::T_CATINV_STOCK_STATUS_F_STOCK_ID => Cfg::INV_OLD_STOCK_ID_DEFAULT,
            Cfg::T_CATINV_STOCK_STATUS_F_QTY => $qty,
            Cfg::T_CATINV_STOCK_STATUS_F_STOCK_STATUS => $status
        ];
        $conn->insert($table, $bind);
    }

}