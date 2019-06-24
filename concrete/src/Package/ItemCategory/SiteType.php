<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Site\Type\Service;

defined('C5_EXECUTE') or die('Access Denied.');

class SiteType extends AbstractCategory
{

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function getItemCategoryDisplayName()
    {
        return t('Site Types');
    }

    public function getItemName($type)
    {
        return $type->getSiteTypeName();
    }

    public function getPackageItems(Package $package)
    {
        return $this->service->getByPackage($package);
    }

    public function removeItem($item)
    {
        $this->service->delete($item);
    }

}
