<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;

use Concrete\Core\Entity\Validation\BannedWord;
use Concrete\Core\Page\Controller\DashboardPageController;

class BannedWords extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('bannedWords', $this->getBannedWords());
        $this->set('bannedListEnabled', $config->get('conversations.banned_words'));
    }

    public function getBannedWords(): array
    {
        $repository = $this->entityManager->getRepository(BannedWord::class);
        return $repository->findAll();
    }

    public function save()
    {
        if (!$this->token->validate("update_banned_words")) {
            $this->error->add('Invalid Token.');

            return false;
        }

        $words = (array) $this->post('banned_word');
        $words = array_map('strtolower', $words);
        $repository = $this->entityManager->getRepository(BannedWord::class);
        $objects = $repository->findAll();

        /** @var BannedWord $object */
        foreach ($objects as $object) {
            $word = $object->getWord();
            if (!in_array($word, $words, true)) {
                $this->entityManager->remove($object);
            }
        }

        foreach ($words as $word) {
            $object = $repository->findOneBy(['bannedWord' => $word]);
            if (!is_object($object)) {
                $instance = new BannedWord();
                $instance->setWord($word);
                $this->entityManager->persist($instance);
            }
        }

        $this->entityManager->flush();

        $config = $this->app->make('config');
        $config->save('conversations.banned_words', (bool) $this->post('banned_list_enabled'));

        $this->flash('success', t('Updated Banned Words.'));

        return $this->buildRedirect([$this->getPageObject(), 'view']);
    }
}
