<?php
namespace Concrete\Core\Export\Item\Express\Control;

use Concrete\Core\Export\Item\Express\Control;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class AssociationControl extends Control
{

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\AssociationControl
     * @param \SimpleXMLElement $xml
     */
    public function export($control, \SimpleXMLElement $xml)
    {
        $node = parent::export($control, $xml);
        $node->addAttribute('association', $control->getAssociation()->getID());
        $node->addAttribute('label-mask', $control->getAssociationEntityLabelMask());

    }

}
