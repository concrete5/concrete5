<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Entity\Attribute\Set;
use Concrete\Core\Export\Item\ItemInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeSet implements ItemInterface
{

    /**
     * @param $set Set
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     */
    public function export($set, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('attributeset');
        $node->addAttribute('name', $set->getAttributeSetName());
        $node->addAttribute('package', $set->getPackageHandle());
        $node->addAttribute('handle', $set->getAttributeSetHandle());
        $node->addAttribute('category', $set->getAttributeKeyCategory()->getAttributeKeyCategoryHandle());

        foreach($set->getAttributeKeys() as $setKey) {
            $key = $node->addChild('attributekey');
            $key->addAttribute('handle', $setKey->getAttributeKeyHandle());
        }

        return $node;
    }

}