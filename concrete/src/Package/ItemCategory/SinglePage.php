<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\SinglePagesItemList;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Page\Single;

defined('C5_EXECUTE') or die('Access Denied.');

class SinglePage extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Single Pages');
    }

    public function getItemName($page)
    {
        return $page->getCollectionName();
    }

    public function renderList(Package $package)
    {
        $controller = new SinglePagesItemList($this, $package);
        $controller->render();
    }

    public function getPackageItems(Package $package)
    {
        return Single::getListByPackage($package);
    }

}
