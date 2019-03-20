<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Helper;

class Product
{
    /** @var int */
    private $cacheAttrSetId = null;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $repoAttrSet;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface\Proxy $repoAttrSet
    ) {
        $this->manObj = $manObj;
        $this->resource = $resource;
        $this->repoAttrSet = $repoAttrSet;
    }

    /**
     * Retrieve attribute set ID.
     */
    public function getAttributeSetId()
    {
        if (is_null($this->cacheAttrSetId)) {
            /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
            $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
            $list = $this->repoAttrSet->getList($crit);
            $items = $list->getItems();
            $attrSet = reset($items);
            $this->cacheAttrSetId = $attrSet->getId();
        }
        return $this->cacheAttrSetId;
    }

    /**
     * Get product ID by product SKU (SKU should be unique, first result only will be returned for products
     * with the same SKUs).
     *
     * @param string $sku
     * @return int|null
     * @throws \Zend_Db_Statement_Exception
     */
    public function getIdBySku($sku)
    {
        $result = null;
        $conn = $this->resource->getConnection();
        $query = $conn->select();
        $entity = [\Magento\Catalog\Model\Product::ENTITY, 'entity'];
        $table = $this->resource->getTableName($entity);
        $query->from($table, ['entity_id']);
        $query->where("sku=:sku");
        $rs = $conn->query($query, ['sku' => $sku]);
        $all = $rs->fetchAll();
        if (count($all)) {
            $one = reset($all);
            $result = $one['entity_id'];
        }
        return $result;
    }
}