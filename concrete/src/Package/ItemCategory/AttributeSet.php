<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Attribute\Set;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeSet extends AbstractCategory
{

    protected $entityManager;

    public function getItemCategoryDisplayName()
    {
        return t('Attribute Sets');
    }

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function removeItem($set)
    {
        $this->entityManager->remove($set);
        $this->entityManager->flush();
    }

    public function getItemName($set)
    {
        return $set->getAttributeSetDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Set::getListByPackage($package);
    }

}
