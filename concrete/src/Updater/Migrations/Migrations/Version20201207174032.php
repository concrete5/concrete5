<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20201207174032 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType("image");
        $this->refreshEntities([
            Type::class
        ]);

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);

        $db->executeQuery("UPDATE FileImageThumbnailTypes SET ftAvailableInBlocks = 1");
    }
}