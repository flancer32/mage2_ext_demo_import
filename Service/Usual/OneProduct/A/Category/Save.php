<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Service\Usual\OneProduct\A\Category;


class Save
{
    /** @var array [name => id] */
    private $cacheCatsByName;
    /** @var \Magento\Catalog\Api\Data\CategoryInterfaceFactory */
    private $factCat;
    /** @var \Magento\Catalog\Api\CategoryRepositoryInterface */
    private $repoCat;
    /** @var \Magento\Catalog\Model\ResourceModel\Category\Tree */
    private $resCatTree;

    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoCat,
        \Magento\Catalog\Api\Data\CategoryInterfaceFactory $factCat,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $resCatTree
    ) {
        $this->repoCat = $repoCat;
        $this->factCat = $factCat;
        $this->resCatTree = $resCatTree;
    }

    /**
     * @param \Flancer32\DemoImport\Api\Data\Category[] $cats
     */
    public function exec($cats)
    {
        if (is_array($cats)) {
            $map = $this->getMap();
            foreach ($cats as $cat) {
                $name = $cat->name;
                $this->repoCat->get();
            }
        }
    }

    private function getMap()
    {
        if (!$this->cacheCatsByName) {
            $cats = [];
            $all = $this->resCatTree->getCollection();
            foreach ($all as $one) {
                $id = $one->getId();
                $name = $one->getName();
                $cats[$name] = $id;
            }
            $this->cacheCatsByName = $cats;
        }
        return $this->cacheCatsByName;
    }
}