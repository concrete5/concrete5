<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Core\Workflow\Progress\Category;

defined('C5_EXECUTE') or die('Access Denied.');

class Workflow extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Workflows');
    }

    /**
     * @param $workflow Workflow
     * @return mixed
     */
    public function getItemName($workflow)
    {
        return $workflow->getWorkflowName();
    }

    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Workflow\Workflow::getListByPackage($package);
    }

}
