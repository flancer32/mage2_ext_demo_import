<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Direct\OneProduct\A;

use Flancer32\DemoImport\Config as Cfg;

/**
 * Locally used (in 'Flancer32\DemoImport\Service\Direct\OneProduct' only) helper to create product's media data.
 */
class Media
{
    /** @var \Flancer32\DemoImport\Helper\Media */
    private $hlpMedia;
    /** @var \Flancer32\DemoImport\Helper\Product\Eav */
    private $hlpProdEav;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Flancer32\DemoImport\Helper\Media $hlpMedia,
        \Flancer32\DemoImport\Helper\Product\Eav $hlpProdEav
    ) {
        $this->resource = $resource;
        $this->hlpMedia = $hlpMedia;
        $this->hlpProdEav = $hlpProdEav;
    }

    /**
     * Save product's media data into DB (using old structure & new MSI structure).
     *
     * @param int $prodId
     * @param string $mediaFullPath full path to media image
     * @return string path to media relative to catalog products media root ('/i/m/image.png')
     */
    public function create($prodId, $mediaFullPath)
    {
        /* move image file under Magento './pub/media/...' */
        $mediaPathPrefixed = $this->saveImage($mediaFullPath);
        /* save path to media file into registry */
        $mediaId = $this->createGallery($mediaPathPrefixed);
        /* then link new media with product */
        $this->createGalleryValue($mediaId, $prodId);
        $this->createGalleryValueToEntity($mediaId, $prodId);
        $result = $mediaPathPrefixed;
        return $result;
    }

    /**
     * Register media file in gallery.
     *
     * @param string $imgPathPrefixed
     * @return int
     */
    private function createGallery($mediaPathPrefixed)
    {
        $attrId = $this->hlpProdEav->getIdByCode(Cfg::ATTR_PROD_MEDIA_GALLERY);
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $tblName = Cfg::TBL_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY;
        $table = $this->resource->getTableName($tblName);
        $bind = [
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_F_ATTRIBUTE_ID => $attrId,
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_F_VALUE => $mediaPathPrefixed,
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_F_MEDIA_TYPE => Cfg::ATTR_GALLERY_TYPE_IMAGE,
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_F_DISABLED => false
        ];
        $conn->insert($table, $bind);
        $result = $conn->lastInsertId($table);
        return $result;
    }

    /**
     * Link media with product and with store view.
     *
     * @param int $mediaId
     * @param int $prodId
     * @return int catalog_product_entity_media_gallery_value.record_id
     */
    private function createGalleryValue($mediaId, $prodId)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $tblName = Cfg::TBL_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE;
        $table = $this->resource->getTableName($tblName);
        $bind = [
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_VALUE_ID => $mediaId,
            /* use admin store view by default */
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_STORE_ID => Cfg::STORE_ID_ADMIN,
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_ENTITY_ID => $prodId,
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_LABEL => null,
            /* we have one only image */
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_POSITION => 1,
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_DISABLED => false
        ];
        $conn->insert($table, $bind);
        $result = $conn->lastInsertId($table);
        return $result;
    }

    /**
     * Link between product and media (default media for the product?).
     *
     * @param $mediaId
     * @param $prodId
     */
    private function createGalleryValueToEntity($mediaId, $prodId)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $conn */
        $conn = $this->resource->getConnection();
        $tblName = Cfg::TBL_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_TO_ENTITY;
        $table = $this->resource->getTableName($tblName);
        $bind = [
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_TO_ENTITY_F_VALUE_ID => $mediaId,
            Cfg::T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_TO_ENTITY_F_ENTITY_ID => $prodId
        ];
        $conn->insert($table, $bind);
    }

    /**
     * Save source image into './pub/media/catalog/product/n/a/name.jpg' and return path being related to
     * product catalog media root ('/m/k/mkm42_04_31.png')
     *
     * @param string $source "/.../vendor/flancer32/mage2_ext_demo_import/etc/data/img/mkm42_04_31.png"
     * @return string string "/m/k/mkm42_04_31.png"
     * @throws \Exception
     */
    private function saveImage($source)
    {
        $fullPath = realpath($source);
        $filename = basename($fullPath);
        $dirMedia = $this->hlpMedia->getDirPubMediaCatalog();
        $prefix = $this->hlpMedia->getPathPrefixForName($filename);
        $dirTarget = $dirMedia . $prefix;
        $this->hlpMedia->makeDir($dirTarget);
        $target = $dirTarget . DIRECTORY_SEPARATOR . $filename;
        copy($source, $target);
        $result = $prefix . DIRECTORY_SEPARATOR . $filename;
        return $result;
    }


}