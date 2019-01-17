<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Test\Flancer32\DemoImport\Service\Usual;
include_once(__DIR__ . '/../../phpunit_bootstrap.php');

use Flancer32\DemoImport\Service\Usual\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Usual\OneProduct\Response as AResponse;

class OneProductTest
    extends \PHPUnit\Framework\TestCase
{
    private const A_SKU = 'sku';

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
        /** @var \Flancer32\DemoImport\Service\Usual\OneProduct $service */
        $service = $obm->get(\Flancer32\DemoImport\Service\Usual\OneProduct::class);

        $req = new ARequest();
        $req->sku = self::A_SKU;
        $res = $service->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }
}