<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Direct\OneProduct\A;

use Flancer32\DemoImport\Config as Cfg;

/**
 * Locally used (in 'Flancer32\DemoImport\Service\Direct\OneProduct' only) helper to create product's EAV data.
 */
class Attr
{
    /** @var \Flancer32\DemoImport\Helper\Product\Eav */
    private $hlpProdEav;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Flancer32\DemoImport\Helper\Product\Eav $hlpProdEav
    ) {
        $this->resource = $resource;
        $this->hlpProdEav = $hlpProdEav;
    }

    /**
     * Create new product and return ID of the new product.
     *
     * @param int $prodId
     * @param string $attrCode
     * @param string $attrValue
     * @throws \Zend_Db_Statement_Exception
     */
    public function create($prodId, $attrCode, $attrValue)
    {
        $attrId = $this->hlpProdEav->getIdByCode($attrCode);
        $attrType = $this->hlpProdEav->getTypeByCode($attrCode);
        if ($attrId) {
            /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
            $conn = $this->resource->getConnection();
            $tblName = Cfg::TBL_CATALOG_PRODUCT_ENTITY . '_' . $attrType;
            $table = $this->resource->getTableName($tblName);
            $bind = [
                Cfg::EAV_ATTRIBUTE_ID => $attrId,
                Cfg::EAV_ENTITY_ID => $prodId,
                /* put all attributes to default store view with id=0 (admin) */
                Cfg::EAV_STORE_ID => Cfg::STORE_ID_ADMIN,
                Cfg::EAV_VALUE => $attrValue
            ];
            $conn->insert($table, $bind);
        }
    }

}