<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\AddressValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="AddressAttributeKeyTypes")
 */
class AddressType extends Type
{
    public function getAttributeValue()
    {
        return new AddressValue();
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $defaultCountry = '';

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hasCustomCountries = false;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $customCountries = array();

    /**
     * @return mixed
     */
    public function getDefaultCountry()
    {
        return $this->defaultCountry;
    }

    /**
     * @param mixed $defaultCountry
     */
    public function setDefaultCountry($defaultCountry)
    {
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * @return mixed
     */
    public function hasCustomCountries()
    {
        return $this->hasCustomCountries;
    }

    /**
     * @param mixed $hasCustomCountries
     */
    public function setHasCustomCountries($hasCustomCountries)
    {
        $this->hasCustomCountries = $hasCustomCountries;
    }

    /**
     * @return mixed
     */
    public function getCustomCountries()
    {
        return $this->customCountries;
    }

    /**
     * @param mixed $customCountries
     */
    public function setCustomCountries($customCountries)
    {
        $this->customCountries = $customCountries;
    }

}
