<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Form implements ItemInterface
{

    /**
     * @param $form \Concrete\Core\Entity\Express\Form
     * @param \SimpleXMLElement $xml
     */
    public function export($form, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('form');
        $node->addAttribute('id', $form->getID());
        $node->addAttribute('name', $form->getName());
        /**
         * @var $set FieldSet
         */
        if (count($form->getFieldSets()) > 0) {
            $sets = $node->addChild('fieldsets');
            foreach($form->getFieldSets() as $set) {
                $fieldset = $sets->addChild('fieldset');
                $fieldset->addAttribute('title', $set->getTitle());
                $fieldset->addAttribute('description', $set->getDescription());
                $controls = $set->getControls();
                if (count($controls) > 0) {
                    $controlsNode = $fieldset->addChild('controls');
                    foreach($controls as $control) {
                        $exporter = $control->getExporter();
                        $exporter->export($control, $controlsNode);
                    }
                }
            }
        }

        return $node;

    }

}
