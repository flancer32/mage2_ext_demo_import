<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Usual;

use Flancer32\DemoImport\Config as Cfg;
use Flancer32\DemoImport\Service\Usual\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Usual\OneProduct\Response as AResponse;

class OneProduct
{
    /** @var \Magento\Catalog\Model\CategoryProductLinkFactory */
    private $factCatProdLink;
    /** @var \Magento\Catalog\Api\Data\ProductInterfaceFactory */
    private $factProd;
    /** @var \Flancer32\DemoImport\Helper\Category\Map */
    private $hlpCatMap;
    /** @var \Flancer32\DemoImport\Helper\Product */
    private $hlpProd;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $manStore;
    /** @var \Magento\Catalog\Api\CategoryLinkRepositoryInterface */
    private $repoCatLink;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $repoCatLink,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $factProd,
        \Magento\Catalog\Model\CategoryProductLinkFactory $factCatProdLink,
        \Flancer32\DemoImport\Helper\Product $hlpProd,
        \Flancer32\DemoImport\Helper\Category\Map $hlpCatMap,
        \Magento\Store\Model\StoreManagerInterface $manStore
    ) {
        $this->repoProd = $repoProd;
        $this->repoCatLink = $repoCatLink;
        $this->factProd = $factProd;
        $this->factCatProdLink = $factCatProdLink;
        $this->hlpProd = $hlpProd;
        $this->hlpCatMap = $hlpCatMap;
        $this->manStore = $manStore;
    }

    public function exec(ARequest $request): AResponse
    {
        $result = new AResponse();
        /* get working vars */
        $prodData = $request->product;
        $typeId = ($prodData->type_id) ?? \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
        $attrSetId = ($prodData->attribute_set_id) ?? $this->hlpProd->getAttributeSetId();
        /* Create/update categories. */
        $this->hlpCatMap->validate($prodData->categories);

        /* import new product into admin store view (TODO: move it to the head import script) */
        $this->manStore->setCurrentStore(Cfg::STORE_ID_ADMIN);

        /* create product entity and fill it with data */
        $prodEntity = $this->factProd->create();
        /* default attrs */
        $prodEntity->setTypeId($typeId);
        $prodEntity->setAttributeSetId($attrSetId);
        /* permanent attributes */
        $prodEntity->setSku($prodData->sku);
        /* required attributes */
        $prodEntity->setName($prodData->name);
        $prodEntity->setPrice($prodData->price);
        /* additional attributes */
        $prodEntity->setData(Cfg::ATTR_PROD_DESC, $prodData->desc);
        $prodEntity->setData(Cfg::ATTR_PROD_DESC_SHORT, $prodData->desc_short);
        /* save product entity into repo (DB) */
        $this->repoProd->save($prodEntity);
        /* link categories with product */
        $this->linkCategories($prodData->sku, $prodData->categories);

        return $result;
    }

    /**
     * Link categories with product.
     *
     * @param string $prodSku
     * @param \Flancer32\DemoImport\Api\Data\Category[] $categories
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function linkCategories($prodSku, $categories)
    {
        if (is_array($categories)) {
            $i = 0;
            foreach ($categories as $one) {
                $name = $one->name;
                $catMageId = $this->hlpCatMap->getIdByName($name);
                /* create new product link if not exists */
                /** @var \Magento\Catalog\Api\Data\CategoryProductLinkInterface $link */
                $link = $this->factCatProdLink->create();
                $link->setCategoryId($catMageId);
                $link->setSku($prodSku);
                $link->setPosition($i++);
                $this->repoCatLink->save($link);
            }
        }
    }
}