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
    const STORE_ID_ADMIN = 0;

    const ATTR_PROD_DESC = 'description';
    const ATTR_PROD_DESC_SHORT = 'short_description';

    /**
     * This module's name.
     */
    const MODULE = self::MODULE_VENDOR . '_' . self::MODULE_PACKAGE;
    const MODULE_PACKAGE = 'DemoImport';
    const MODULE_VENDOR = 'Flancer32';

}