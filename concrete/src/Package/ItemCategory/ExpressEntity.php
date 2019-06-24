<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class ExpressEntity extends AbstractCategory
{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function removeItem($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function getItemCategoryDisplayName()
    {
        return t('Express Entities');
    }

    /**
     * @param $entity Entity
     */
    public function getItemName($entity)
    {
        return $entity->getName();
    }

    public function getPackageItems(Package $package)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        return $r->findByPackage($package);
    }

}
