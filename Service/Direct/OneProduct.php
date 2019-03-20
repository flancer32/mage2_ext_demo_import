<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Direct;

use Flancer32\DemoImport\Service\Direct\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Direct\OneProduct\Response as AResponse;
use Magento\Catalog\Api\Data\ProductInterface as EProd;

class OneProduct
{
    /** @var \Flancer32\DemoImport\Helper\Product */
    private $hlpProd;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Flancer32\DemoImport\Helper\Product $hlpProd
    ) {
        $this->resource = $resource;
        $this->hlpProd = $hlpProd;
    }

    /**
     * Create new product and return ID of the new product.
     *
     * @param string $sku
     * @param string $typeId
     * @param int $attrSetId
     * @return int|null
     * @throws \Zend_Db_Statement_Exception
     */
    private function createProduct($sku, $typeId, $attrSetId)
    {
        /** @var \Magento\Framework\App\ResourceConnection $this ->resource */
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $conn */
        $conn = $this->resource->getConnection();
        $entity = [\Magento\Catalog\Model\Product::ENTITY, 'entity'];
        $table = $this->resource->getTableName($entity);
        $bind = [
            EProd::SKU => $sku,
            EProd::TYPE_ID => $typeId,
            EProd::ATTRIBUTE_SET_ID => $attrSetId
        ];
        $conn->insert($table, $bind);
        $result = $this->hlpProd->getIdBySku($sku);
        return $result;
    }

    public function exec(ARequest $request): AResponse
    {
        $result = new AResponse();
        /* get working vars */
        $prodData = $request->product;
        $typeId = ($prodData->type_id) ?? \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
        $attrSetId = ($prodData->attribute_set_id) ?? $this->hlpProd->getAttributeSetId();

        $sku = $prodData->sku;
        $prodId = $this->hlpProd->getIdBySku($sku);
        if (is_null($prodId)) {
            $this->createProduct($sku, $typeId, $attrSetId);
        } else {
            /* product with given SKU already exists */
        }
        return $result;
    }
}