<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Page\Template;

defined('C5_EXECUTE') or die('Access Denied.');

class PageTemplate extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Page Templates');
    }

    public function getItemName($template)
    {
        return $template->getPageTemplateDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Template::getListByPackage($package);
    }

}
