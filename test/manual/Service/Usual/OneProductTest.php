<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Test\Flancer32\DemoImport\Service\Usual;
include_once(__DIR__ . '/../../phpunit_bootstrap.php');

use Flancer32\DemoImport\Api\Data\Category as DCategory;
use Flancer32\DemoImport\Api\Data\Product as DProduct;
use Flancer32\DemoImport\Service\Usual\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Usual\OneProduct\Response as AResponse;

class OneProductTest
    extends \PHPUnit\Framework\TestCase
{
    private const CAT_1_NAME = 'category 3';
    private const CAT_2_NAME = 'category 4';
    private const NAME = 'name';
    private const PRICE = 12.34;
    private const SKU = 'sku';

    public static function setUpBeforeClass()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\State $state */
        $state = $obm->get(\Magento\Framework\App\State::class);
        try {
            $state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        } catch (\Throwable $e) {
            /* stealth all exceptions */
        }
    }

    public function test_exec()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();

        /* create product data item */
        $prod = new DProduct();
        $prod->sku = self::SKU;
        $prod->name = self::NAME;
        $prod->price = self::PRICE;
        $cat1 = new DCategory();
        $cat1->name = self::CAT_1_NAME;
        $cat2 = new DCategory();
        $cat2->name = self::CAT_2_NAME;
        $prod->categories = [$cat1, $cat2];

        /* use service to create new product entity */
        /** @var \Flancer32\DemoImport\Service\Usual\OneProduct $service */
        $service = $obm->get(\Flancer32\DemoImport\Service\Usual\OneProduct::class);
        $req = new ARequest();
        $req->product = $prod;
        $res = $service->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }
}