<?php
namespace Concrete\Core\Export\Item\Express\Control;

use Concrete\Core\Export\Item\Express\Control;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Utility\Service\Xml;

defined('C5_EXECUTE') or die('Access Denied.');

class TextControl extends Control
{

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\TextControl
     * @param \SimpleXMLElement $xml
     */
    public function export($control, \SimpleXMLElement $xml)
    {
        $node = parent::export($control, $xml);
        $node->addAttribute('type-id', 'text');
        $service = new Xml();
        $service->createCDataNode($node, 'headline', $control->getHeadline());
        $service->createCDataNode($node, 'body', $control->getBody());
    }

}
