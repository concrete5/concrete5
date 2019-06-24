<?php
namespace Concrete\Core\Import\Item\Express\Control;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Import\Item\Express\ItemInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class EntityPropertyControl implements ItemInterface
{

    public function import(\SimpleXMLElement $xml, Entity $entity)
    {
        switch((string) $xml['type-id']) {
            case 'text':
            default:
                $control = new \Concrete\Core\Entity\Express\Control\TextControl();
                $control->setHeadline((string) $xml->headline);
                $control->setBody((string) $xml->body);
                break;
        }

        $control->setCustomLabel((string) $xml['custom-label']);
        if (((string) $xml['required']) == '1') {
            $control->setIsRequired(true);
        }
        $control->setId((string) $xml['id']);
        return $control;
    }

}
