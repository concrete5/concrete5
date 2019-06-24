<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Antispam\Library;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class AntispamLibrary extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Antispam Libraries');
    }

    public function getItemName($library)
    {
        return $library->getSystemAntispamLibraryName();
    }

    public function getPackageItems(Package $package)
    {
        return Library::getListByPackage($package);
    }

}
