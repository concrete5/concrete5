<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class StackFolder implements ItemInterface
{

    /**
     * @param $folder \Concrete\Core\Page\Stack\Folder\Folder
     * @param \SimpleXMLElement $xml
     * @return mixed
     */
    public function export($folder, \SimpleXMLElement $xml)
    {
        $page = $folder->getPage();
        $node = $xml->addChild('folder');
        $node->addAttribute('name', \Core::make('helper/text')->entities($page->getCollectionName()));
        $node->addAttribute('path', substr($page->getCollectionPath(), strlen(STACKS_PAGE_PATH)));

        return $node;
    }

}
