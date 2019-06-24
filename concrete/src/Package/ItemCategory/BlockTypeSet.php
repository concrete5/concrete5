<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class BlockTypeSet extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Block Type Sets');
    }

    public function getItemName($set)
    {
        return $set->getBlockTypeSetDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Set::getListByPackage($package);
    }

}
