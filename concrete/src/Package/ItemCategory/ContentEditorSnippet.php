<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Editor\Snippet;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class ContentEditorSnippet extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Content Editor Snippet');
    }

    public function getItemName($snippet)
    {
        return $snippet->getSystemContentEditorSnippetName();
    }

    public function getPackageItems(Package $package)
    {
        return Snippet::getListByPackage($package);
    }

}
