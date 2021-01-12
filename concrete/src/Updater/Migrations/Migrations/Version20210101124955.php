<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Validation\BannedWord;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210101124955 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([BannedWord::class]);
    }
}
