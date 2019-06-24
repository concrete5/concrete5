<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Permission\Key\Key;

defined('C5_EXECUTE') or die('Access Denied.');

class PermissionKey extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Permission Keys');
    }

    public function getItemName($key)
    {
        return $key->getPermissionKeyDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Key::getListByPackage($package);
    }

}
