<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class AuthenticationType extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Authentication Types');
    }

    public function getItemName($type)
    {
        return $type->getAuthenticationTypeDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Authentication\AuthenticationType::getListByPackage($package);
    }

}
