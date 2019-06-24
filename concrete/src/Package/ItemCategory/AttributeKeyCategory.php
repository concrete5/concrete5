<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeKeyCategory extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Attribute Categories');
    }

    public function removeItem($category)
    {
        $category->getController()->delete();
    }

    public function getItemName($category)
    {
        $txt = \Core::make('helper/text');
        return $txt->unhandle($category->getAttributeKeyCategoryHandle());
    }

    public function getPackageItems(Package $package)
    {
        return Category::getListByPackage($package);
    }

}
