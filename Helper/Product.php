<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\DemoImport\Helper;


class Product
{
    /** @var int */
    private $cacheAttrSetId = null;
    /** @var \Magento\Framework\App\ObjectManager */
    private $manObj;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $repoAttrSet;

    public function __construct(
        \Magento\Framework\App\ObjectManager $manObj,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface\Proxy $repoAttrSet
    ) {
        $this->manObj = $manObj;
        $this->repoAttrSet = $repoAttrSet;
    }

    /**
     * Retrieve attribute set ID.
     */
    public function getAttributeSetId()
    {
        if (is_null($this->cacheAttrSetId)) {
            /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
            $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
            $list = $this->repoAttrSet->getList($crit);
            $items = $list->getItems();
            $attrSet = reset($items);
            $this->cacheAttrSetId = $attrSet->getId();
        }
        return $this->cacheAttrSetId;
    }
}