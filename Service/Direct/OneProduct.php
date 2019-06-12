<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Direct;

use Flancer32\DemoImport\Config as Cfg;
use Flancer32\DemoImport\Service\Direct\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Direct\OneProduct\Response as AResponse;
use Magento\Catalog\Api\Data\ProductAttributeInterface as EProdAttr;
use Magento\Catalog\Api\Data\ProductInterface as EProd;
use Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor as ATaxModel;

class OneProduct
{
    /** @var \Flancer32\DemoImport\Service\Direct\OneProduct\A\Attr */
    private $aAttr;
    /** @var \Flancer32\DemoImport\Service\Direct\OneProduct\A\Category */
    private $aCat;
    /** @var \Flancer32\DemoImport\Service\Direct\OneProduct\A\Media */
    private $aMedia;
    /** @var \Flancer32\DemoImport\Service\Direct\OneProduct\A\Product */
    private $aProd;
    /** @var \Flancer32\DemoImport\Service\Direct\OneProduct\A\Stock */
    private $aStock;
    /** @var \Magento\Framework\Filter\TranslitUrl */
    private $filterTranslit;
    /** @var \Flancer32\DemoImport\Helper\Category\Map */
    private $hlpCatMap;
    /** @var \Flancer32\DemoImport\Helper\Product */
    private $hlpProd;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filter\TranslitUrl $filterTranslit,
        \Flancer32\DemoImport\Helper\Product $hlpProd,
        \Flancer32\DemoImport\Helper\Category\Map $hlpCatMap,
        \Flancer32\DemoImport\Service\Direct\OneProduct\A\Attr $aAttr,
        \Flancer32\DemoImport\Service\Direct\OneProduct\A\Category $aCat,
        \Flancer32\DemoImport\Service\Direct\OneProduct\A\Media $aMedia,
        \Flancer32\DemoImport\Service\Direct\OneProduct\A\Product $aProd,
        \Flancer32\DemoImport\Service\Direct\OneProduct\A\Stock $aStock
    ) {
        $this->resource = $resource;
        $this->filterTranslit = $filterTranslit;
        $this->hlpProd = $hlpProd;
        $this->hlpCatMap = $hlpCatMap;
        $this->aAttr = $aAttr;
        $this->aCat = $aCat;
        $this->aMedia = $aMedia;
        $this->aProd = $aProd;
        $this->aStock = $aStock;
    }

    /**
     * Create main EAV attributes (short description, name, status, weight, ...).
     *
     * @param int $prodId
     * @param \Flancer32\DemoImport\Api\Data\Product $prodData
     */
    private function createAttrs($prodId, $prodData)
    {
        $desc = $prodData->desc;
        $descShort = $prodData->desc_short;
        $name = $prodData->name;
        $price = $prodData->price;
        $sku = $prodData->sku;
        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
        $taxId = Cfg::TAX_CLASS_ID_GOODS;
        $urlKey = $this->getProductUrlKey($name);
        $visibility = \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH;

        $this->aAttr->create($prodId, EProdAttr::CODE_NAME, $name);
        $this->aAttr->create($prodId, EProdAttr::CODE_PRICE, $price);
        $this->aAttr->create($prodId, EProdAttr::CODE_DESCRIPTION, $desc);
        $this->aAttr->create($prodId, EProdAttr::CODE_SHORT_DESCRIPTION, $descShort);
        $this->aAttr->create($prodId, EProdAttr::CODE_STATUS, $status);
        $this->aAttr->create($prodId, ATaxModel::ATRR_CODE, $taxId);
        $this->aAttr->create($prodId, EProdAttr::CODE_SEO_FIELD_URL_KEY, $urlKey);
        $this->aAttr->create($prodId, EProd::VISIBILITY, $visibility);
    }

    /**
     * Save given image into catalog products media root folder, register media and map roles to the media.
     *
     * @param int $prodId
     * @param string $fileImg fullpath to the image
     * @throws \Zend_Db_Statement_Exception
     */
    private function createMedia($prodId, $fileImg)
    {
        $mediaPath = $this->aMedia->create($prodId, $fileImg);
        /* map roles to image (Base, Small, Thumbnail, Swatch)*/
        $this->aAttr->create($prodId, Cfg::ATTR_PROD_IMAGE, $mediaPath);
        $this->aAttr->create($prodId, Cfg::ATTR_PROD_SMALL_IMAGE, $mediaPath);
        $this->aAttr->create($prodId, Cfg::ATTR_PROD_THUMBNAIL, $mediaPath);
        $this->aAttr->create($prodId, Cfg::ATTR_PROD_SWATCH_IMAGE, $mediaPath);
    }

    public function exec(ARequest $request): AResponse
    {
        $result = new AResponse();
        /* get working vars */
        $prodData = $request->product;
        $typeId = ($prodData->type_id) ?? \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
        $attrSetId = ($prodData->attribute_set_id) ?? $this->hlpProd->getAttributeSetId();
        $sku = $prodData->sku;
        $qty = $prodData->qty;
        $fileImg = $prodData->image_path;
        $categories = $prodData->categories;

        $prodId = $this->hlpProd->getIdBySku($sku);
        if (is_null($prodId)) {
            /* registry product and link it to website */
            $prodId = $this->aProd->create($sku, $typeId, $attrSetId);
            $this->aProd->linkToWebsite($prodId, Cfg::WEBSITE_ID_BASE);
            /* create EAV attributes, stock & media */
            $this->createAttrs($prodId, $prodData);
            $this->aStock->create($prodId, $sku, $qty);
            $this->createMedia($prodId, $fileImg);
            /* link product to categories */
            $this->linkCategories($prodId, $categories);
        } else {
            /* product with given SKU already exists, do nothing */
        }
        return $result;
    }

    /**
     * Convert (cyryllic) product name into URL Key.
     *
     * @param string $productName
     * @return string
     */
    private function getProductUrlKey($productName)
    {
        $result = trim($productName);
        $result = mb_strtolower($result);
        $result = $this->filterTranslit->filter($result);
        $result = str_replace(' ', '-', $result);
        $result = str_replace('\\', '-', $result);
        $result = str_replace('/', '-', $result);
        $result = str_replace('_', '-', $result);
        /* replace '+' with '-plus-' */
        if (strpos($productName, '+') !== false) {
            $result .= '-plus-';
        }
        return $result;
    }

    private function linkCategories($prodId, $categories)
    {
        if (is_array($categories)) {
            /* Create categories in catalog (if not exist). */
            $this->hlpCatMap->validate($categories);

            /* link product to related categories */
            $i = 0;
            foreach ($categories as $one) {
                $name = $one->name;
                $catId = $this->hlpCatMap->getIdByName($name);
                /* create new product link  */
                $this->aCat->create($prodId, $catId);
            }
        }
    }
}