<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\User\Group\GroupList;

defined('C5_EXECUTE') or die('Access Denied.');

class Group extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Groups');
    }

    public function getItemName($set)
    {
        return $set->getGroupDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        $gl = new GroupList();
        $gl->filterByPackage($package);
        return $gl->get();
    }

}
