<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210712110100 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('image');

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $db->executeQuery("UPDATE btContentImage SET sizingOption = 'constrain_size' WHERE maxWidth > 0 OR maxHeight > 0");
    }
}