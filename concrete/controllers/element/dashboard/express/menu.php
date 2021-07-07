<?php
namespace Concrete\Controller\Element\Dashboard\Express;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;

class Menu extends ElementController
{
    protected $currentEntity;
    protected $entityAction = 'admin';

    public function __construct(Entity $entity)
    {
        parent::__construct();
        $this->currentEntity = $entity;
    }

    public function enableViewEntityAction()
    {
        $this->entityAction = 'view';
    }

    public function getElement()
    {
        return 'dashboard/express/menu';
    }

    public function view()
    {
        $r = \ORM::entityManager()->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entities = $r->findBy(array(), array('name' => 'asc'));
        $this->set('types', $entities);
        $this->set('currentType', $this->currentEntity);
        $this->set('entityAction', $this->entityAction);
    }
}
