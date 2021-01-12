<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Validation\BannedWord;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

class ImportBannedWordsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'banned_words';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->banned_words)) {
            $app = Application::getFacadeApplication();
            /** @var EntityManagerInterface $em */
            $em = $app->make(EntityManagerInterface::class);
            $repository = $em->getRepository(BannedWord::class);
            foreach ($sx->banned_words->banned_word as $p) {
                $word = str_rot13((string) $p);
                $bw = $repository->findOneBy(['bannedWord' => $word]);
                if (!is_object($bw)) {
                    $bw = new BannedWord();
                    $bw->setWord($word);
                    $em->persist($bw);
                }
            }
            $em->flush();
        }
    }

}
