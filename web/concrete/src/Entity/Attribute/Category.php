<?php
namespace Concrete\Core\Entity\Attribute;

use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Entity\PackageTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="AttributeKeyCategories", indexes={@Index(name="pkgID", columns={"pkgID", "akCategoryID"}),
 * @Index(name="akCategoryHandle", columns={"akCategoryHandle"})})
 */
class Category implements EntityInterface
{
    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->sets = new ArrayCollection();
    }

    use PackageTrait;

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $akCategoryID;

    /**
     * @Column(type="string", unique=true)
     */
    protected $akCategoryHandle;

    /**
     * @Column(type="integer")
     */
    protected $akCategoryAllowSets;

    /**
     * @ManyToMany(targetEntity="Type", inversedBy="categories")
     * @JoinTable(name="AttributeTypeCategories",
     * joinColumns={@JoinColumn(name="akCategoryID", referencedColumnName="akCategoryID")},
     * inverseJoinColumns={@JoinColumn(name="atID", referencedColumnName="atID")}
     * )
     */
    protected $types;

    /**
     * @OneToMany(targetEntity="Set", mappedBy="category")
     * @JoinColumn(name="akCategoryID", referencedColumnName="asID")
     */
    protected $sets;

    /**
     * @return mixed
     */
    public function getAttributeKeyCategoryID()
    {
        return $this->akCategoryID;
    }

    /**
     * @return mixed
     */
    public function getAttributeKeyCategoryHandle()
    {
        return $this->akCategoryHandle;
    }

    /**
     * @param mixed $akCategoryHandle
     */
    public function setAttributeKeyCategoryHandle($akCategoryHandle)
    {
        $this->akCategoryHandle = $akCategoryHandle;
    }

    /**
     * @return mixed
     */
    public function allowAttributeSets()
    {
        return $this->akCategoryAllowSets;
    }

    /**
     * @param mixed $akCategoryAllowSets
     */
    public function setAllowAttributeSets($akCategoryAllowSets)
    {
        $this->akCategoryAllowSets = $akCategoryAllowSets;
    }

    public function getController()
    {
        $manager = \Core::make('manager/attribute/category');
        $controller = $manager->driver($this->getAttributeKeyCategoryHandle());
        $controller->setCategoryEntity($this);

        return $controller;
    }

    public function clearAttributeKeyCategoryTypes()
    {
        $this->types = new ArrayCollection();
    }

    public function associateAttributeKeyType(Type $type)
    {
        $this->types->add($type);
    }

    public function getAttributeKeyCategory()
    {
        return $this->getController();
    }

    /**
     * @return mixed
     */
    public function getAttributeSets()
    {
        return $this->sets;
    }

    /**
     * @return mixed
     */
    public function getAttributeTypes()
    {
        return $this->types;
    }

    /**
     * @param mixed $types
     */
    public function setAttributeTypes($types)
    {
        $this->types = $types;
    }
}
