<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ExpressEntityEntryAttributeValues"
 * )
 * @ORM\HasLifecycleCallbacks
 * @since 8.0.0
 */
class ExpressValue extends AbstractValue
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry", inversedBy="attributes")
     * @ORM\JoinColumn(name="exEntryID", referencedColumnName="exEntryID"),
     */
    protected $entry;

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    /** @ORM\PreUpdate
     * @since 8.5.0
     */
    public function updateEntryDateModified() {
        if ($this->getEntry() instanceof \Concrete\Core\Entity\Express\Entry) {
            $this->getEntry()->updateDateModified();
        }
    }


}
