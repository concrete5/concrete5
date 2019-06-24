<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Page\Type\Type;

defined('C5_EXECUTE') or die('Access Denied.');

class PageType extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Page Types');
    }

    public function getItemName($type)
    {
        return $type->getPageTypeDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }

}
