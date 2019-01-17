<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Cli\Import;

use Flancer32\DemoImport\Service\Usual\OneProduct\Request as ARequest;
use Flancer32\DemoImport\Service\Usual\OneProduct\Response as AResponse;

/**
 * Console command to import products from JSON.
 */
class Products
    extends \Symfony\Component\Console\Command\Command
{
    private const BUNCH_SIZE = 100;
    private const DESC = 'Import products from JSON (products catalog initialization).';
    private const NAME = 'fl32:import:prod';
    /* CLI option to import all JSON data w/o any limitations. */
    private const OPT_ALL_DEFAULT = 'no';
    private const OPT_ALL_NAME = 'all';
    private const OPT_ALL_SHORTCUT = 'a';
    /* CLI option to set limitations on data import. */
    private const OPT_LIMIT_DEFAULT = 100;
    private const OPT_LIMIT_NAME = 'limit';
    private const OPT_LIMIT_SHORTCUT = 'l';
    /* CLI option to set full path to imported JSON data. */
    private const OPT_PATH_NAME = 'path';
    private const OPT_PATH_SHORTCUT = 'p';

    /** @var \Magento\Framework\App\State */
    private $appState;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var \Flancer32\DemoImport\Service\Usual\OneProduct */
    private $servUsualProd;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\State $appState,
        \Flancer32\DemoImport\Service\Usual\OneProduct $servUsualProd
    ) {
        parent::__construct(self::NAME);
        /* Symfony related config is called from parent constructor */
        $this->setDescription(self::DESC);
        /* own properties */
        $this->manObj = $manObj;
        $this->appState = $appState;
        $this->servUsualProd = $servUsualProd;

        /* add command options */
        $this->addOption(
            self::OPT_ALL_NAME,
            self::OPT_ALL_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            "Set 'yes' to import all lines, 'limit' option is ignored in this case (default: no).",
            self::OPT_ALL_DEFAULT
        );
        $this->addOption(
            self::OPT_LIMIT_NAME,
            self::OPT_LIMIT_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            "Limit number of records for importing (default: 100).",
            self::OPT_LIMIT_DEFAULT
        );
        $this->addOption(
            self::OPT_PATH_NAME,
            self::OPT_PATH_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            "Full path to JSON file with data to import into Magneto Catalog."
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
        $all = (string)$input->getOption(self::OPT_ALL_NAME);
        $limit = (int)$input->getOption(self::OPT_LIMIT_NAME);
        $path = (string)$input->getOption(self::OPT_PATH_NAME);
        $msg = 'Arguments: ' . self::OPT_ALL_NAME . "=$all; ";
        $msg .= self::OPT_LIMIT_NAME . "=$limit; ";
        $msg .= self::OPT_PATH_NAME . "=$path; ";
        $output->writeln($msg);

        /** perform operation */
        $this->checkAreaCode();

        $this->testUsualProduct();

//        /* read JSON */
//        $json = $this->readJson($path);
//        $total = count($json);
//        $output->writeln("Total $total records read from input JSON.");
//        /* define number of lines to import */
//        if ($all == self::OPT_ALL_DEFAULT) {
//            if ($limit <= 0) {
//                $limit = self::OPT_LIMIT_DEFAULT;
//            }
//            $json = array_slice($json, 0, $limit);
//        }
//
//        /* process JSON data */
//        $bunchSize = self::BUNCH_SIZE;
//        $total = count($json);
//        $output->writeln("Importing $total records using $bunchSize rows bunches (see \${MAGE}/var/log/system.log to trace)...");


        /** compose result */
        $dateCompleted = date('Y-m-d H:i:s');
        $output->writeln("Import is started at '$dateStarted' and is completed at '$dateCompleted'.");
        $output->writeln("Command '{$this->getName()}' is executed.");
    }

    /**
     * @param string $path full path to JSON file with import data.
     * @return array parsed data as associative array.
     */
    private function readJson($path)
    {
        $content = file_get_contents($path);
        $result = json_decode($content);
        return $result;
    }

    private function testUsualProduct()
    {
        $req = new ARequest();
        $req->sku = self::A_SKU;
        /** @var AResponse $resp */
        $resp = $this->servUsualProd->exec($req);
    }
}