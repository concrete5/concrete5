<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Entity\Express\Entity as ExpressEntity;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Tree\Node\Node;

defined('C5_EXECUTE') or die('Access Denied.');

class Entity implements ItemInterface
{

    /**
     * @param $entity ExpressEntity
     * @param \SimpleXMLElement $xml
     */
    public function export($entity, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('entity');
        $node->addAttribute('id', $entity->getID());
        $node->addAttribute('handle', $entity->getHandle());
        $node->addAttribute('plural_handle', $entity->getPluralHandle());
        $node->addAttribute('name', $entity->getName());
        $node->addAttribute('supports_custom_display_order', $entity->supportsCustomDisplayOrder() ? '1' : '');
        $node->addAttribute('include_in_public_list', $entity->getIncludeInPublicList() ? '1' : '');
        $node->addAttribute('description', h($entity->getDescription()));
        $node->addAttribute('default_view_form', $entity->getDefaultViewForm()->getID());
        $node->addAttribute('default_edit_form', $entity->getDefaultEditForm()->getID());

        $results = Node::getByID($entity->getEntityResultsNodeId());
        if (is_object($results)) {
            $parent = $results->getTreeNodeParentObject();
            $path = $parent->getTreeNodeDisplayPath();
            $node->addAttribute('results-folder', $path);
        }

        $attributes = $entity->getAttributes();
        if (count($attributes)) {
            $asn = $node->addChild('attributekeys');
            foreach($attributes as $ak) {
                $exporter = $ak->getExporter();
                $exporter->export($ak, $asn);
            }
        }

        $associations = $entity->getAssociations();
        if (count($associations)) {
            $asn = $node->addChild('associations');
            foreach($associations as $association) {
                $exporter = $association->getExporter();
                $exporter->export($association, $asn);
            }
        }

        $forms = $entity->getForms();
        if (count($forms)) {
            $fsn = $node->addChild('forms');
            foreach($forms as $form) {
                $exporter = $form->getExporter();
                $exporter->export($form, $fsn);
            }
        }

        return $node;

    }

}
