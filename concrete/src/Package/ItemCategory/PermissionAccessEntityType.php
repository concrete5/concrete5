<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Core\Permission\Access\Entity\Type;

defined('C5_EXECUTE') or die('Access Denied.');

class PermissionAccessEntityType extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Permission Access Entity Types');
    }

    public function getItemName($type)
    {
        return $type->getAccessEntityTypeDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }

}
