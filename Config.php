<?php
/**
 * Container for module's constants (hardcoded configuration).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport;

use Magento\Catalog\Api\Data\ProductInterface as EProd;
use Magento\CatalogInventory\Api\Data\StockItemInterface as EStockItem;
use Magento\CatalogInventory\Api\Data\StockStatusInterface as EStockStatus;
use Magento\Eav\Api\Data\AttributeInterface as EEavAttr;
use Magento\Eav\Model\Entity as EBase;
use Magento\InventoryApi\Api\Data\SourceItemInterface as ESourceItem;

interface Config
{
    /**
     * Names for attributes of an entities.
     */
    const ATTR_GALLERY_FILE = 'file';
    const ATTR_GALLERY_IMAGES = 'images';
    const ATTR_GALLERY_LABEL = 'label';
    const ATTR_GALLERY_MEDIA_TYPE = 'media_type';
    const ATTR_GALLERY_TYPE_IMAGE = 'image';

    const ATTR_INVENTORY_IS_IN_STOCK = 'is_in_stock';
    const ATTR_INVENTORY_QTY = 'qty';

    const ATTR_PROD_DESC = 'description';
    const ATTR_PROD_DESC_SHORT = 'short_description';
    const ATTR_PROD_IMAGE = 'image';
    const ATTR_PROD_MEDIA_GALLERY = 'media_gallery';
    const ATTR_PROD_QTY_AND_STOCK_STATUS = 'quantity_and_stock_status';
    const ATTR_PROD_SMALL_IMAGE = 'small_image';
    const ATTR_PROD_SWATCH_IMAGE = 'swatch_image';
    const ATTR_PROD_THUMBNAIL = 'thumbnail';

    /**
     * Field names for EAV related tables (with suffixes '_datetime', '_decimal', '_int', '_text', '_varchar').
     * Sample: catalog_product_entity_datetime.
     */
    const EAV_ATTRIBUTE_ID = 'attribute_id';
    const EAV_ENTITY_ID = 'entity_id';
    const EAV_STORE_ID = 'store_id';
    const EAV_VALUE = 'value';
    const EAV_VALUE_ID = 'value_id';

    const INV_NEW_STOCK_CODE_DEFAULT = 'default';
    const INV_OLD_STOCK_ID_DEFAULT = 1;
    /**
     * This module's name.
     */
    const MODULE = self::MODULE_VENDOR . '_' . self::MODULE_PACKAGE;
    const MODULE_PACKAGE = 'DemoImport';
    const MODULE_VENDOR = 'Flancer32';

    /**
     * see data in 'store' table.
     */
    const STORE_ID_ADMIN = 0;
    const STORE_ID_DEFAULT = 1;

    /** see: ./magento/module-tax/Setup/Patch/Data/AddTaxAttributeAndTaxClasses.php:100 */
    const TAX_CLASS_ID_GOODS = 2;

    /**
     * DB tables names.
     */
    const TBL_CATALOGINVENTORY_STOCK_ITEM = 'cataloginventory_stock_item';
    const TBL_CATALOGINVENTORY_STOCK_STATUS = 'cataloginventory_stock_status';
    const TBL_CATALOG_CATEGORY_PRODUCT = 'catalog_category_product';
    const TBL_CATALOG_PRODUCT_ENTITY = 'catalog_product_entity';
    const TBL_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY = 'catalog_product_entity_media_gallery';
    const TBL_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE = 'catalog_product_entity_media_gallery_value';
    const TBL_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_TO_ENTITY = 'catalog_product_entity_media_gallery_value_to_entity';
    const TBL_CATALOG_PRODUCT_WEBSITE = 'catalog_product_website';
    const TBL_EAV_ATTRIBUTE = 'eav_attribute';
    const TBL_INVENTORY_SOURCE_ITEM = 'inventory_source_item';

    /**
     * Fields names for the tables.
     */
    const T_CATINV_STOCK_ITEM_F_IS_IN_STOCK = EStockItem::IS_IN_STOCK;
    const T_CATINV_STOCK_ITEM_F_IS_QTY_DECIMAL = EStockItem::IS_QTY_DECIMAL;
    const T_CATINV_STOCK_ITEM_F_MANAGE_STOCK = EStockItem::MANAGE_STOCK;
    const T_CATINV_STOCK_ITEM_F_NOTIFY_STOCK_QTY = EStockItem::NOTIFY_STOCK_QTY;
    const T_CATINV_STOCK_ITEM_F_PRODUCT_ID = EStockItem::PRODUCT_ID;
    const T_CATINV_STOCK_ITEM_F_QTY = EStockItem::QTY;
    const T_CATINV_STOCK_ITEM_F_QTY_INCREMENTS = EStockItem::QTY_INCREMENTS;
    const T_CATINV_STOCK_ITEM_F_STOCK_ID = EStockItem::STOCK_ID;
    const T_CATINV_STOCK_ITEM_F_WEBSITE_ID = 'website_id';
    const T_CATINV_STOCK_STATUS_F_PRODUCT_ID = EStockStatus::PRODUCT_ID;
    const T_CATINV_STOCK_STATUS_F_QTY = EStockStatus::QTY;
    const T_CATINV_STOCK_STATUS_F_STOCK_ID = EStockStatus::STOCK_ID;
    const T_CATINV_STOCK_STATUS_F_STOCK_STATUS = EStockStatus::STOCK_STATUS;
    const T_CATINV_STOCK_STATUS_F_WEBSITE_ID = 'website_id';
    const T_CAT_CAT_PROD_F_CATEGORY_ID = 'category_id';
    const T_CAT_CAT_PROD_F_ENTITY_ID = 'entity_id';
    const T_CAT_CAT_PROD_F_POSITION = 'position';
    const T_CAT_CAT_PROD_F_PRODUCT_ID = 'product_id';
    const T_CAT_PROD_ENTITY_F_ATTRIBUTE_SET_ID = EProd::ATTRIBUTE_SET_ID;
    const T_CAT_PROD_ENTITY_F_ENTITY_ID = EBase::DEFAULT_ENTITY_ID_FIELD;
    const T_CAT_PROD_ENTITY_F_SKU = EProd::SKU;
    const T_CAT_PROD_ENTITY_F_TYPE_ID = EProd::TYPE_ID;
    const T_CAT_PROD_ENTITY_MEDIA_GAL_F_ATTRIBUTE_ID = 'attribute_id';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_F_DISABLED = 'disabled';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_F_MEDIA_TYPE = 'media_type';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_F_VALUE = 'value';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_F_VALUE_ID = 'value_id';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_DISABLED = 'disabled';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_ENTITY_ID = 'entity_id';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_LABEL = 'label';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_POSITION = 'position';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_RECORD_ID = 'record_id';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_STORE_ID = 'store_id';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_F_VALUE_ID = 'value_id';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_TO_ENTITY_F_ENTITY_ID = 'entity_id';
    const T_CAT_PROD_ENTITY_MEDIA_GAL_VAL_TO_ENTITY_F_VALUE_ID = 'value_id';
    const T_CAT_PROD_WS_F_PRODUCT_ID = 'product_id';
    const T_CAT_PROD_WS_F_WEBSITE_ID = 'website_id';
    const T_EAV_ATTR_F_ATTRIBUTE_CODE = EEavAttr::ATTRIBUTE_CODE;
    const T_EAV_ATTR_F_ATTRIBUTE_ID = EEavAttr::ATTRIBUTE_ID;
    const T_EAV_ATTR_F_BACKEND_TYPE = EEavAttr::BACKEND_TYPE;
    const T_EAV_ATTR_F_ENTITY_TYPE_ID = EEavAttr::ENTITY_TYPE_ID;
    const T_INV_SOURCE_ITEM_F_QUANTITY = ESourceItem::QUANTITY;
    const T_INV_SOURCE_ITEM_F_SKU = ESourceItem::SKU;
    const T_INV_SOURCE_ITEM_F_SOURCE_CODE = ESourceItem::SOURCE_CODE;
    const T_INV_SOURCE_ITEM_F_SOURCE_ITEM_ID = 'source_item_id';
    const T_INV_SOURCE_ITEM_F_STATUS = ESourceItem::STATUS;

    /**
     * see data in 'store_website' table.
     */
    const WEBSITE_ID_ADMIN = 0;
    const WEBSITE_ID_BASE = 1;
}