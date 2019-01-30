<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Regular;

use Flancer32\DemoImport\Config as Cfg;
use Flancer32\DemoImport\Service\Regular\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Regular\OneProduct\Response as AResponse;

class OneProduct
{
    /** @var \Magento\Catalog\Model\CategoryProductLinkFactory */
    private $factCatProdLink;
    /** @var \Magento\Catalog\Api\Data\ProductInterfaceFactory */
    private $factProd;
    /** @var \Flancer32\DemoImport\Helper\Category\Map */
    private $hlpCatMap;
    /** @var \Flancer32\DemoImport\Helper\Media */
    private $hlpMedia;
    /** @var \Flancer32\DemoImport\Helper\Product */
    private $hlpProd;
    /** @var \Magento\Catalog\Model\Product\Gallery\CreateHandler */
    private $hndlGalleryCreate;
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
        \Flancer32\DemoImport\Helper\Media $hlpMedia,
        \Flancer32\DemoImport\Helper\Category\Map $hlpCatMap,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Magento\Catalog\Model\Product\Gallery\CreateHandler $hndlGalleryCreate
    ) {
        $this->repoProd = $repoProd;
        $this->repoCatLink = $repoCatLink;
        $this->factProd = $factProd;
        $this->factCatProdLink = $factCatProdLink;
        $this->hlpProd = $hlpProd;
        $this->hlpMedia = $hlpMedia;
        $this->hlpCatMap = $hlpCatMap;
        $this->manStore = $manStore;
        $this->hndlGalleryCreate = $hndlGalleryCreate;
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
    private function categoriesLink($prodSku, $categories)
    {
        if (is_array($categories)) {
            /* Create categories in catalog. */
            $this->hlpCatMap->validate($categories);

            /* link product to categories */
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

    public function exec(ARequest $request): AResponse
    {
        $result = new AResponse();
        /* get working vars */
        $prodData = $request->product;
        $typeId = ($prodData->type_id) ?? \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
        $attrSetId = ($prodData->attribute_set_id) ?? $this->hlpProd->getAttributeSetId();

        /* import new product into admin store view */
        $this->manStore->setCurrentStore(Cfg::STORE_ID_ADMIN);

        /* create product entity and fill it with data */
        $product = $this->factProd->create();
        /* default attrs */
        $product->setTypeId($typeId);
        $product->setAttributeSetId($attrSetId);
        /* permanent attributes */
        $product->setSku($prodData->sku);
        /* required attributes */
        $product->setName($prodData->name);
        $product->setPrice($prodData->price);
        /* additional attributes */
        $product->setData(Cfg::ATTR_PROD_DESC, $prodData->desc);
        $product->setData(Cfg::ATTR_PROD_DESC_SHORT, $prodData->desc_short);
        /* inventory data */
        $inventory = [
            Cfg::ATTR_INVENTORY_IS_IN_STOCK => true,
            Cfg::ATTR_INVENTORY_QTY => $prodData->qty
        ];
        $product->setData(Cfg::ATTR_PROD_QTY_AND_STOCK_STATUS, $inventory);
        /* save product entity into repo (DB) */
        $this->repoProd->save($product);

        /* add image to the product */
        $this->imageAdd($prodData->sku, $prodData->image_path);

        /* link categories with product */
        $this->manStore->setCurrentStore(Cfg::STORE_ID_DEFAULT);
        $this->categoriesLink($prodData->sku, $prodData->categories);

        return $result;
    }

    /**
     * @param string $sku
     * @param string $imagePath file path to the image source (jpg, png).
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function imageAdd($sku, $imagePath)
    {
        $imagePathRelative = $this->imagePlaceToTmpMedia($imagePath);
        /* reload product with gallery data */
        $product = $this->repoProd->get($sku);
        /* add image to product's gallery */
        $gallery[Cfg::ATTR_GALLERY_IMAGES][] = [
            Cfg::ATTR_GALLERY_FILE => $imagePathRelative,
            Cfg::ATTR_GALLERY_MEDIA_TYPE => Cfg::ATTR_GALLERY_TYPE_IMAGE
        ];
        $product->setData(Cfg::ATTR_PROD_MEDIA_GALLERY, $gallery);
        /* set usage areas */
        $product->setData(Cfg::ATTR_PROD_IMAGE, $imagePathRelative);
        $product->setData(Cfg::ATTR_PROD_SMALL_IMAGE, $imagePathRelative);
        $product->setData(Cfg::ATTR_PROD_SWATCH_IMAGE, $imagePathRelative);
        $product->setData(Cfg::ATTR_PROD_THUMBNAIL, $imagePathRelative);
        /* create product's gallery */
        $this->hndlGalleryCreate->execute($product);
    }

    /**
     * Place image $name into '.../pub/media/tmp/catalog/product then return relative path.
     *
     * @param string $source file path to the image source (jpg, png).
     * @return string file name with prefix: 'default.jpg' => '/d/e/default.jpg'
     * @throws \Exception
     */
    private function imagePlaceToTmpMedia($source)
    {
        $fullPath = realpath($source);
        $filename = basename($fullPath);
        $dirMedia = $this->hlpMedia->getDirPubMediaCatalog();
        $prefix = $this->hlpMedia->getPathPrefixForName($filename);
        $dirTarget = $dirMedia . $prefix;
        $this->hlpMedia->makeDir($dirTarget);
        $target = $this->hlpMedia->getImagePlacement($filename);
        copy($source, $target);
        return $prefix . DIRECTORY_SEPARATOR . $filename;
    }
}