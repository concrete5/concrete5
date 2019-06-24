<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Stack implements ItemInterface
{

    /**
     * @param $stack \Concrete\Core\Page\Stack\Stack
     * @param \SimpleXMLElement $xml
     * @return mixed
     */
    public function export($stack, \SimpleXMLElement $xml)
    {
        $db = \Database::connection();
        $node = $xml->addChild('stack');
        $node->addAttribute('name', \Core::make('helper/text')->entities($stack->getCollectionName()));
        if ($stack->getStackTypeExportText()) {
            $node->addAttribute('type', $stack->getStackTypeExportText());
        }
        $node->addAttribute('path', substr($stack->getCollectionPath(), strlen(STACKS_PAGE_PATH)));

        // you shouldn't ever have a sub area in a stack but just in case.
        $r = $db->Execute('select arHandle from Areas where cID = ? and arParentID = 0', array($stack->getCollectionID()));
        while ($row = $r->FetchRow()) {
            $ax = \Area::get($stack, $row['arHandle']);
            $ax->export($node, $stack);
        }

        return $node;
    }

}
