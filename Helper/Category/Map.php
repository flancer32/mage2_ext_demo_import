<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Helper\Category;

/**
 * Load categories and create map [name => id], validate existence of the given categories (create and add to map
 * if missed).
 */
class Map
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
     * Create new inactive category with given name under 'Default Category'.
     *
     * @param string $name
     * @return int ID of the new category
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function createCategory($name)
    {
        $cat = $this->factCat->create();
        $cat->setName($name);
        $cat->setIsActive(false);
        $saved = $this->repoCat->save($cat);
        $result = $saved->getId();
        return $result;
    }

    /**
     * Get cached map of the categories names.
     *
     * @return array [name => id]
     */
    public function get()
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

    /**
     * @param string $name
     * @return int|null
     */
    public function getIdByName($name)
    {
        $result = null;
        if (!$this->cacheCatsByName) {
            $this->get();
        }
        $norm = strtolower(trim($name));
        if (isset($this->cacheCatsByName[$norm])) {
            $result = $this->cacheCatsByName[$norm];
        }
        return $result;
    }

    /**
     * Validate existence of the given categories (create and add to map if missed).
     *
     * @param \Flancer32\DemoImport\Api\Data\Category[] $cats
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function validate($cats)
    {
        if (is_array($cats)) {
            $map = $this->get();
            $names = array_keys($map);
            foreach ($cats as $cat) {
                $name = strtolower(trim($cat->name));
                if (!in_array($name, $names)) {
                    $newId = $this->createCategory($name);
                    $this->cacheCatsByName[$name] = $newId;
                }
            }
        }
    }
}