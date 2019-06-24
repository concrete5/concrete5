<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Page\Type\PublishTarget\Type\Type;

defined('C5_EXECUTE') or die('Access Denied.');

class PageTypePublishTargetType extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Page Type Publish Target Types');
    }

    public function getItemName($type)
    {
        return $type->getPageTypePublishTargetTypeDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }

}
