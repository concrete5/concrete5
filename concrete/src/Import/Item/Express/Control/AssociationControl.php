<?php
namespace Concrete\Core\Import\Item\Express\Control;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Import\ImportableInterface;
use Concrete\Core\Import\Item\Express\ItemInterface;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die('Access Denied.');

class AssociationControl implements ItemInterface
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function import(\SimpleXMLElement $xml, Entity $entity)
    {
        $control = new \Concrete\Core\Entity\Express\Control\AssociationControl();
        $control->setCustomLabel((string) $xml['custom-label']);
        if (((string) $xml['required']) == '1') {
            $control->setIsRequired(true);
        }
        $control->setId((string) $xml['id']);
        $association = $this->entityManager->find('Concrete\Core\Entity\Express\Association', (string) $xml['association']);
        $control->setAssociation($association);
        return $control;
    }

}
