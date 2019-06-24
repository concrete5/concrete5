<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Control implements ItemInterface
{

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\Control
     * @param \SimpleXMLElement $xml
     */
    public function export($control, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('control');
        $node->addAttribute('id', $control->getID());
        $node->addAttribute('type', $control->getType());
        $node->addAttribute('required', $control->isRequired() ? 1 : '');
        $node->addAttribute('custom-label', $control->getCustomLabel());
        return $node;

    }

}
