<?php
namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Support\Manager as CoreManager;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die('Access Denied.');

class Manager extends CoreManager
{
    protected $entityManager;

    public function createEntityPropertyDriver()
    {
        return new EntityPropertyType();
    }

    public function createAttributeKeyDriver()
    {
        return new AttributeKeyType($this->entityManager);
    }

    public function createAssociationDriver()
    {
        return new AssociationType($this->entityManager);
    }

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->driver('entity_property');
        $this->driver('attribute_key');
        $this->driver('association');
    }
}
