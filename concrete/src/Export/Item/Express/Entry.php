<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Entity\Express\Entry as ExpressEntry;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Utility\Service\Identifier;

defined('C5_EXECUTE') or die('Access Denied.');

class Entry implements ItemInterface
{

    protected $store;

    public function __construct(EntryStore $store)
    {
        $this->store = $store;
    }

    /**
     * @param $entry ExpressEntry
     * @param \SimpleXMLElement $xml
     */
    public function export($entry, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('entry');
        $node->addAttribute('id', $this->store->convertNumericEntryIdToIdentifier($entry->getId()));
        $node->addAttribute('label', $entry->getLabel());
        $node->addAttribute('entity', $entry->getEntity()->getHandle());
        $node->addAttribute('display-order', $entry->getEntryDisplayOrder());
        $attribs = $entry->getAttributes();

        if (count($attribs) > 0) {
            $attributes = $node->addChild('attributes');
            foreach ($attribs as $av) {
                $cnt = $av->getController();
                $cnt->setAttributeValue($av);
                $akx = $attributes->addChild('attributekey');
                $akx->addAttribute('handle', $av->getAttributeKey()->getAttributeKeyHandle());
                $cnt->exportValue($akx);
            }
        }

        $associations = $entry->getAssociations();
        if (count($associations)) {
            $associationsNode = $node->addChild('associations');
            foreach($associations as $association) {
                $child = $associationsNode->addChild('association');
                $child->addAttribute('target', $association->getAssociation()->getComputedTargetPropertyName());
                $associationEntriesNode = $child->addChild('entries');
                foreach($association->getSelectedEntries() as $associationEntry) {
                    $id = $this->store->convertNumericEntryIdToIdentifier($associationEntry->getId());
                    $associationEntryNode = $associationEntriesNode->addChild('entry');
                    $associationEntryNode->addAttribute('entry', $id);
                }
            }
        }
        return $node;

    }

}
