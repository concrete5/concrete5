<?php
namespace Concrete\Core\Validation\BannedWord;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
 */
class BannedWord extends ConcreteObject
{
    protected $id;
    protected $word;
    /** @var \Concrete\Core\Entity\Validation\BannedWord */
    protected $entity;

    /**
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @param $word
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public function setWord($word)
    {
        if ($word == false) {
            return $this->delete();
        }
        if (is_object($this->entity)) {
            $app = Application::getFacadeApplication();
            /** @var EntityManagerInterface $em */
            $em = $app->make(EntityManagerInterface::class);
            $this->entity->setWord($word);
            $em->persist($this->entity);
            $em->flush();
        }
        $this->word = $word;
    }

    public function __construct($id = false, $word = false)
    {
        $this->init($id, $word);
    }

    /**
     * @param $id
     * @param $word
     * @throws \Exception
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public function init($id, $word)
    {
        if ($this->id && $this->word) {
            throw new \Exception(t('Banned word already initialized.'));
        }
        $this->id = $id;
        $this->word = $word;

        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $em */
        $em = $app->make(EntityManagerInterface::class);
        $repository = $em->getRepository(\Concrete\Core\Entity\Validation\BannedWord::class);
        $this->entity = $repository->find($id);
    }

    /**
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public function delete()
    {
        if (is_object($this->entity)) {
            $app = Application::getFacadeApplication();
            /** @var EntityManagerInterface $em */
            $em = $app->make(EntityManagerInterface::class);
            $em->remove($this->entity);
            $em->flush();
            $this->entity = null;
        }
    }

    /**
     * @param $id
     * @return BannedWord|false
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public static function getByID($id)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $em */
        $em = $app->make(EntityManagerInterface::class);
        $repository = $em->getRepository(\Concrete\Core\Entity\Validation\BannedWord::class);
        /** @var \Concrete\Core\Entity\Validation\BannedWord $word */
        $word = $repository->find($id);
        if (!is_object($word)) {
            return false;
        }

        return new static($word->getID(), $word->getWord());
    }

    /**
     * @param $word
     * @return BannedWord|false
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public static function getByWord($word)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $em */
        $em = $app->make(EntityManagerInterface::class);
        $repository = $em->getRepository(\Concrete\Core\Entity\Validation\BannedWord::class);
        $word = strtolower($word);
        /** @var \Concrete\Core\Entity\Validation\BannedWord $bw */
        $bw = $repository->findOneBy(['bannedWord' => $word]);
        if (!is_object($bw)) {
            return false;
        }

        return new static($bw->getID(), $bw->getWord());
    }

    /**
     * @param $word
     * @return BannedWord|false
     * @deprecated Use \Concrete\Core\Entity\Validation\BannedWord instead
     */
    public static function add($word)
    {
        if (!$word) {
            return false;
        }

        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $em */
        $em = $app->make(EntityManagerInterface::class);

        $word = strtolower($word);
        if ($bw = static::getByWord($word)) {
            return $bw;
        }

        $bw = new \Concrete\Core\Entity\Validation\BannedWord();
        $bw->setWord($word);
        $em->persist($bw);
        $em->flush();

        return new static($bw->getID(), $bw->getWord());
    }
}
