<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class Job extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Jobs');
    }

    public function getItemName($job)
    {
        return $job->getJobName();
    }

    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Job\Job::getListByPackage($package);
    }

}
