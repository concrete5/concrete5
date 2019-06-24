<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Captcha\Library;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class CaptchaLibrary extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Captcha Libraries');
    }

    public function getItemName($library)
    {
        return $library->getSystemCaptchaLibraryName();
    }

    public function getPackageItems(Package $package)
    {
        return Library::getListByPackage($package);
    }

}
