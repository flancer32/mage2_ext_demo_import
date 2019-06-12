<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Direct\OneProduct\A;

use Flancer32\DemoImport\Config as Cfg;

/**
 * Locally used (in 'Flancer32\DemoImport\Service\Direct\OneProduct' only) helper to create product data.
 */
class Product
{
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Create new product and return ID of the new product.
     *
     * @param string $sku
     * @param string $typeId
     * @param int $attrSetId
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    public function create($sku, $typeId, $attrSetId)
    {
        /** @var \Magento\Framework\App\ResourceConnection $this ->resource */
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::TBL_CATALOG_PRODUCT_ENTITY);
        $bind = [
            Cfg::T_CAT_PROD_ENTITY_F_SKU => $sku,
            Cfg::T_CAT_PROD_ENTITY_F_TYPE_ID => $typeId,
            Cfg::T_CAT_PROD_ENTITY_F_ATTRIBUTE_SET_ID => $attrSetId
        ];
        $conn->insert($table, $bind);
        $result = $conn->lastInsertId($table);
        return $result;
    }

    /**
     * Create link between new product and website.
     *
     * @param int $prodId
     * @param int $websiteId
     */
    public function linkToWebsite($prodId, $websiteId)
    {
        /** @var \Magento\Framework\App\ResourceConnection $this ->resource */
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::TBL_CATALOG_PRODUCT_WEBSITE);
        $bind = [
            Cfg::T_CAT_PROD_WS_F_PRODUCT_ID => $prodId,
            Cfg::T_CAT_PROD_WS_F_WEBSITE_ID => $websiteId
        ];
        $conn->insert($table, $bind);
    }
}