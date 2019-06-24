<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Page\Type\Composer\Control\Type\Type;

defined('C5_EXECUTE') or die('Access Denied.');

class PageTypeComposerControlType extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Page Type Composer Control Types');
    }

    public function getItemName($type)
    {
        return $type->getPageTypeComposerControlTypeDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }

}
