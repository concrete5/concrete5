<?php

namespace Concrete\Core\Entity\Validation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BannedWords")
 */
class BannedWord
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $bwID;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $bannedWord = '';

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->bwID;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->bannedWord;
    }

    /**
     * @param string $bannedWord
     */
    public function setWord(string $bannedWord): void
    {
        $this->bannedWord = $bannedWord;
    }
}
