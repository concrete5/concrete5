<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class BlockType extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Block Types');
    }

    public function getItemName($type)
    {
        return $type->getBlockTypeDisplayName();
    }

    public function renderList(Package $package)
    {
        $controller = new BlockTypeItemList($this, $package);
        $controller->render();
    }

    public function getPackageItems(Package $package)
    {
        $list = new BlockTypeList();
        $list->filterByPackage($package);
        return $list->get();
    }

}
