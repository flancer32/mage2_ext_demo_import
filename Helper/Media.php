<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Helper;

/**
 * Compose paths to filesystem objects to process media.
 */
class Media
{
    /** @var string absolute path to '/pub/media' */
    private $cacheDirPubMedia;
    /** @var string absolute path to '/pub/media/catalog/product' */
    private $cacheDirPubMediaCatalog;
    /** @var string absolute path to '/pub/media/tmp/catalog/product' */
    private $cacheDirPubMediaCatalogTmp;
    /** @var \Magento\Catalog\Model\Product\Media\Config */
    private $mediaConfig;

    public function __construct(
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig
    ) {
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * Return absolute path to media folder ('./pub/media/').
     *
     * @return string
     * @throws \Exception
     */
    public function getDirPubMedia()
    {
        if (is_null($this->cacheDirPubMedia)) {
            $relative = BP . '/pub/media/';
            $absolute = realpath($relative);
            if (is_dir($absolute)) {
                $this->cacheDirPubMedia = $absolute;
            } else {
                $created = mkdir($relative, 0770, true);
                if ($created) {
                    $absolute = realpath($relative);
                    $this->cacheDirPubMedia = $absolute;
                } else {
                    throw  new \Exception("Cannot create directory: '$absolute'.");
                }
            }
        }
        return $this->cacheDirPubMedia;
    }

    /**
     * Return absolute path to products catalog media folder ('./pub/media/catalog/product/').
     *
     * @return string
     * @throws \Exception
     */
    public function getDirPubMediaCatalog()
    {
        if (is_null($this->cacheDirPubMediaCatalog)) {
            $absolute = $this->getDirPubMedia() . DIRECTORY_SEPARATOR . $this->mediaConfig->getBaseMediaPath();
            if (is_dir($absolute)) {
                $this->cacheDirPubMediaCatalog = $absolute;
            } else {
                $created = mkdir($absolute, 0770, true);
                if ($created) {
                    $this->cacheDirPubMediaCatalog = $absolute;
                } else {
                    throw  new \Exception("Cannot create directory: '$absolute'.");
                }
            }
        }
        return $this->cacheDirPubMediaCatalog;
    }


    public function getDirPubMediaCatalogTmp()
    {
        if (is_null($this->cacheDirPubMediaCatalogTmp)) {
            $absolute = $this->getDirPubMedia() . DIRECTORY_SEPARATOR . $this->mediaConfig->getBaseTmpMediaPath();
            if (is_dir($absolute)) {
                $this->cacheDirPubMediaCatalogTmp = $absolute;
            } else {
                $created = mkdir($absolute, 0770, true);
                if ($created) {
                    $this->cacheDirPubMediaCatalogTmp = $absolute;
                } else {
                    throw  new \Exception("Cannot create directory: '$absolute'.");
                }
            }
        }
        return $this->cacheDirPubMediaCatalogTmp;

    }

    /**
     * 'default.jpg' =>
     *
     * @param string $filename
     * @return string
     * @throws \Exception
     */
    public function getImagePlacement(string $filename)
    {
        $prefix = $this->getPathPrefixForName($filename);
        $dirTmp = $this->getDirPubMediaCatalogTmp();
        $dirTarget = $dirTmp . DIRECTORY_SEPARATOR . $prefix;
        $this->makeDir($dirTarget);
        $result = $dirTarget . DIRECTORY_SEPARATOR . $filename;
        return $result;
    }

    /**
     * Return '/a/b' for 'aBcDeF' (to place multiple files in different catalogs).
     *
     * @param string $name
     * @return string
     */
    public function getPathPrefixForName($name)
    {
        $norm = trim(strtolower($name));
        $a = $norm[0];
        $b = $norm[1];
        $result = DIRECTORY_SEPARATOR . $a . DIRECTORY_SEPARATOR . $b;
        return $result;
    }

    /**
     * Create directory if not exist.
     *
     * @param string $fullPath
     */
    public function makeDir($fullPath)
    {
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0770, true);
        }
    }
}