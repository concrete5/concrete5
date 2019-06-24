<?php
namespace Concrete\Core\Export\Item\Express\Control;

use Concrete\Core\Export\Item\Express\Control;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeKeyControl extends Control
{

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\AttributeKeyControl
     * @param \SimpleXMLElement $xml
     */
    public function export($control, \SimpleXMLElement $xml)
    {
        $node = parent::export($control, $xml);
        $child = $node->addChild('attributekey');
        $child->addAttribute('handle', $control->getAttributeKey()->getAttributeKeyHandle());
        return $node;
    }

}
