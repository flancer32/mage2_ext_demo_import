<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Usual;

use Flancer32\DemoImport\Service\Usual\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Usual\OneProduct\Response as AResponse;

class OneProduct
{
    /** @var \Flancer32\DemoImport\Service\Usual\OneProduct\A\Category\Save */
    private $aCatSave;
    /** @var \Magento\Catalog\Api\Data\ProductInterfaceFactory */
    private $factProd;
    /** @var \Flancer32\DemoImport\Helper\Product */
    private $hlpProd;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $factProd,
        \Flancer32\DemoImport\Helper\Product $hlpProd,
        \Flancer32\DemoImport\Service\Usual\OneProduct\A\Category\Save $aCatSave
    ) {
        $this->repoProd = $repoProd;
        $this->factProd = $factProd;
        $this->hlpProd = $hlpProd;
        $this->aCatSave = $aCatSave;
    }

    public function exec(ARequest $request): AResponse
    {
        $result = new AResponse();
        /* get working vars */
        $prodData = $request->product;
        $typeId = ($prodData->type_id) ?? \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
        $attrSetId = ($prodData->attribute_set_id) ?? $this->hlpProd->getAttributeSetId();
        /* Create/update categories. */
        $this->aCatSave->exec($prodData->categories);

        /* create product entity and fill it with data */
        $prodEntity = $this->factProd->create();
        /* default attrs */
        $prodEntity->setTypeId($typeId);
        $prodEntity->setAttributeSetId($attrSetId);
        /* permanent attributes */
        $prodEntity->setSku($prodData->sku);
        /* additional attributes */
        $prodEntity->setName($prodData->name);
        $prodEntity->setPrice($prodData->price);
        /* save product entity into repo (DB) */
        $this->repoProd->save($prodEntity);

        return $result;
    }

}