<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\User\Point\Action\Action;

defined('C5_EXECUTE') or die('Access Denied.');

class UserPointAction extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('User Point Actions');
    }

    public function getItemName($action)
    {
        return $action->getUserPointActionName();
    }

    public function getPackageItems(Package $package)
    {
        return Action::getListByPackage($package);
    }

}
