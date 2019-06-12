<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Direct\OneProduct\A;

use Flancer32\DemoImport\Config as Cfg;

/**
 * Locally used (in 'Flancer32\DemoImport\Service\Direct\OneProduct' only) helper to link product to category.
 */
class Category
{
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Create link between product & category.
     *
     * @param int $prodId
     * @param int $catId
     */
    public function create($prodId, $catId)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $tblName = Cfg::TBL_CATALOG_CATEGORY_PRODUCT;
        $table = $this->resource->getTableName($tblName);
        $bind = [
            Cfg::T_CAT_CAT_PROD_F_CATEGORY_ID => $catId,
            Cfg::T_CAT_CAT_PROD_F_PRODUCT_ID => $prodId,
        ];
        $conn->insert($table, $bind);
    }

}