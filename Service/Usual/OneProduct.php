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
    /** @var \Magento\Catalog\Api\Data\ProductInterfaceFactory */
    private $factProd;
    /** @var \Flancer32\DemoImport\Helper\Product */
    private $hlpProd;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $factProd,
        \Flancer32\DemoImport\Helper\Product $hlpProd
    ) {
        $this->repoProd = $repoProd;
        $this->factProd = $factProd;
        $this->hlpProd = $hlpProd;
    }

    public function exec(ARequest $request): AResponse
    {
        $result = new AResponse();
        $typeId = ($request->type_id) ?? \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
        $attrSetId = ($request->attribute_set_id) ?? $this->hlpProd->getAttributeSetId();
        $prod = $this->factProd->create();
        $prod->setSku($request->sku);
        $prod->setTypeId($typeId);
        $prod->setAttributeSetId($attrSetId);
        $this->repoProd->save($prod);

        return $result;
    }
}