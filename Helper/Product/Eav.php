<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Helper\Product;

use Flancer32\DemoImport\Config as Cfg;
use Magento\Catalog\Setup\CategorySetup as Setup;

/**
 * Helper for products EAV attributes. This helper creates maps for attributes IDs & types being accessed by
 * attributes codes.
 */
class Eav
{
    /** @var int[] */
    private $cacheIds;
    /** @var string[] */
    private $cacheTypes;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Get product attribute ID by it's code.
     *
     * @param string $attrCode
     * @return int
     */
    public function getIdByCode($attrCode)
    {
        $result = 0;
        if (is_null($this->cacheIds)) {
            $this->load();
        }
        if (isset($this->cacheIds[$attrCode])) {
            $result = $this->cacheIds[$attrCode];
        }
        return $result;
    }

    /**
     * Get product attribute type by it's code.
     *
     * @param string $attrCode
     * @return string [datetime | decimal | int | text | varchar]
     */
    public function getTypeByCode($attrCode)
    {
        $result = '';
        if (is_null($this->cacheTypes)) {
            $this->load();
        }
        if (isset($this->cacheTypes[$attrCode])) {
            $result = $this->cacheTypes[$attrCode];
        }
        return $result;
    }

    /**
     * Load data from DB and save its to object cache.
     */
    private function load()
    {
        $this->cacheIds = $this->cacheTypes = [];
        $conn = $this->resource->getConnection();
        $query = $conn->select();
        $table = $this->resource->getTableName(Cfg::TBL_EAV_ATTRIBUTE);
        $query->from($table, [
            Cfg::T_EAV_ATTR_F_ATTRIBUTE_CODE,
            Cfg::T_EAV_ATTR_F_ATTRIBUTE_ID,
            Cfg::T_EAV_ATTR_F_BACKEND_TYPE
        ]);
        $query->where(Cfg::T_EAV_ATTR_F_ENTITY_TYPE_ID . '=:typeId');
        $rs = $conn->query($query, ['typeId' => Setup::CATALOG_PRODUCT_ENTITY_TYPE_ID]);
        $all = $rs->fetchAll();
        if (is_array($all)) {
            foreach ($all as $one) {
                $attrId = $one[Cfg::T_EAV_ATTR_F_ATTRIBUTE_ID];
                $attrCode = $one[Cfg::T_EAV_ATTR_F_ATTRIBUTE_CODE];
                $backendType = $one[Cfg::T_EAV_ATTR_F_BACKEND_TYPE];
                $this->cacheIds[$attrCode] = $attrId;
                $this->cacheTypes[$attrCode] = $backendType;
            }
        }
    }
}