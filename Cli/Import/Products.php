<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Cli\Import;

use Flancer32\DemoImport\Api\Data\Category as DCategory;
use Flancer32\DemoImport\Api\Data\Product as DProduct;
use Flancer32\DemoImport\Service\Regular\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Regular\OneProduct\Response as AResponse;

/**
 * Console command to import products from JSON.
 */
class Products
    extends \Symfony\Component\Console\Command\Command
{
    private const DESC = 'Import products from JSON (products catalog initialization).';
    private const NAME = 'fl32:import:prod';
    /* CLI option to switch import type (regular|direct). */
    private const OPT_TYPE_DEFAULT = self::TYPE_REGULAR;
    private const OPT_TYPE_NAME = 'type';
    private const OPT_TYPE_SHORTCUT = 't';
    private const TYPE_DIRECT = 'direct';
    private const TYPE_REGULAR = 'regular';
    /** @var \Magento\Framework\App\State */
    private $appState;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var \Flancer32\DemoImport\Service\Regular\OneProduct */
    private $servRegularProd;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\State $appState,
        \Flancer32\DemoImport\Service\Regular\OneProduct $servRegularProd
    ) {
        parent::__construct(self::NAME);
        /* Symfony related config is called from parent constructor */
        $this->setDescription(self::DESC);
        /* own properties */
        $this->manObj = $manObj;
        $this->appState = $appState;
        $this->servRegularProd = $servRegularProd;

        /* add command options */
        $this->addOption(
            self::OPT_TYPE_NAME,
            self::OPT_TYPE_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            "Set type for products import (regular|direct, default: regular).",
            self::OPT_TYPE_DEFAULT
        );
    }

    /**
     * Sets area code to start a adminhtml session and configure Object Manager.
     */
    private function checkAreaCode()
    {
        try {
            /* area code should be set only once */
            $this->appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_GLOBAL;
            $this->appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $this->manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $this->manObj->configure($config);
        }
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /** define local working data */
        $output->writeln("Command '{$this->getName()}' is started.");
        $dateStarted = date('Y-m-d H:i:s');
        $type = (string)$input->getOption(self::OPT_TYPE_NAME);
        $msg = 'Arguments: ' . self::OPT_TYPE_NAME . "=$type; ";
        $output->writeln($msg);

        /** perform operation */
        $this->checkAreaCode();

        /* read JSON */
        $products = $this->readJson();
        $total = count($products);
        $output->writeln("Total $total records read from input JSON.");

        if ($type == self::TYPE_DIRECT) {
            /* TODO: add direct import here */
        } else {
            $this->importRegular($products);
        }

        /** compose result */
        $dateCompleted = date('Y-m-d H:i:s');
        $output->writeln("Import is started at '$dateStarted' and is completed at '$dateCompleted'.");
        $output->writeln("Command '{$this->getName()}' is executed.");
    }

    /**
     * @param DProduct $products
     */
    private function importRegular($products)
    {
        foreach ($products as $product) {
            $req = new ARequest();
            $req->product = $product;
            /** @var AResponse $resp */
            $resp = $this->servRegularProd->exec($req);
        }
    }

    /**
     * Read JSON with import data.
     *
     * @return DProduct[] parsed data
     */
    private function readJson()
    {
        $result = [];
        $path = __DIR__ . '/../../etc/data/products.json';
        $content = file_get_contents($path);
        $all = json_decode($content);
        /**
         * Convert JSON item to data object.
         */
        /* all images has relative paths in JSON */
        $imageBasePath = realpath(__DIR__ . '/../../etc/data/img');
        foreach ($all as $one) {
            $entry = new DProduct();
            $entry->sku = $one->sku;
            $entry->name = $one->name;
            $entry->desc = $one->desc;
            $entry->desc_short = $one->desc_short;
            $entry->price = $one->price;
            $entry->qty = $one->qty;
            $imagePath = $imageBasePath . DIRECTORY_SEPARATOR . $one->image_path;
            $imagePath = realpath($imagePath);
            $entry->image_path = $imagePath;
            $entry->categories = [];
            if (is_array($one->categories)) {
                foreach ($one->categories as $name) {
                    $category = new DCategory();
                    $category->name = $name;
                    $entry->categories[] = $category;
                }
            }
            $result[] = $entry;
        }
        return $result;
    }
}