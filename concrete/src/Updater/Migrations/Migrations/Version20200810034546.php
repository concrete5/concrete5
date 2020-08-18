<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Job\Job;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20200810034546 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $job = Job::getByHandle('delete_invalidated_users');
        if ($job === null) {
            Job::installByHandle('delete_invalidated_users');
        }
    }
}
