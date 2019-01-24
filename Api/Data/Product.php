<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Api\Data;

/**
 * Data structure for product item to be imported.
 */
class Product
{
    /** @var int */
    public $attribute_set_id;
    /** @var \Flancer32\DemoImport\Api\Data\Category[] */
    public $categories;
    /** @var string */
    public $desc;
    /** @var string */
    public $desc_short;
    /** @var string */
    public $name;
    /** @var float */
    public $price;
    /** @var string */
    public $sku;
    /** @var string */
    public $type_id;
}