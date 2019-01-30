<?php
/**
 * Container for module's constants (hardcoded configuration).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport;

interface Config
{
    /**
     * Names for attributes of a entities.
     */
    const ATTR_GALLERY_FILE = 'file';
    const ATTR_GALLERY_IMAGES = 'images';
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
     * This module's name.
     */
    const MODULE = self::MODULE_VENDOR . '_' . self::MODULE_PACKAGE;
    const MODULE_PACKAGE = 'DemoImport';
    const MODULE_VENDOR = 'Flancer32';
    const STORE_ID_ADMIN = 0;
    const STORE_ID_DEFAULT = 1;

}