<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeKey extends AbstractCategory
{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function removeItem($key)
    {
        $this->entityManager->remove($key);
        $this->entityManager->flush();
    }

    public function getItemCategoryDisplayName()
    {
        return t('Attribute Keys');
    }

    public function getItemName($key)
    {
        return $key->getAttributeKeyDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\Key');
        return $r->findByPackage($package);
    }

}
