<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Association implements ItemInterface
{

    /**
     * @param $association \Concrete\Core\Entity\Express\Association
     * @param \SimpleXMLElement $xml
     */
    public function export($association, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('association');

        // turns fully qualified class name into one_to_many
        $class = substr(get_class($association), strrpos(get_class($association), '\\') + 1);
        $type = uncamelcase(substr($class, 0, strpos($class, 'Association')));

        $node->addAttribute('type', $type);
        $node->addAttribute('id', $association->getID());
        $node->addAttribute('source-entity', $association->getSourceEntity()->getID());
        $node->addAttribute('target-entity', $association->getTargetEntity()->getID());
        $node->addAttribute('target-property-name', $association->getTargetPropertyName());
        $node->addAttribute('inversed-by-property-name', $association->getInversedByPropertyName());
        $node->addAttribute('is-owned', $association->isOwnedByAssociation() ? '1' : '');
        $node->addAttribute('is-owner', $association->isOwningAssociation() ? '1' : '');

        return $node;

    }

}
