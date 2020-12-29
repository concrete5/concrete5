<?php

namespace Concrete\Job;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\Support\Facade\Application;
use Job as AbstractJob;

class RemoveOldFileAttachments extends AbstractJob
{
    public function getJobName()
    {
        return t("Remove Old File Attachments");
    }

    public function getJobDescription()
    {
        return t("Removes all expired file attachments from private messages.");
    }

    public function run()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $connection */
        $connection = $app->make(Connection::class);
        /** @var Repository $config */
        $config = $app->make(Repository::class);

        $removedItemsCounter = 0;

        $attachmentsLifetime = (int)$config->get("concrete.private_messages.attachments_lifetime", 14);

        $sql = <<<SQL
    SELECT 
        attachments.* 
    FROM 
        UserPrivateMessages AS msg 
    JOIN 
        UserPrivateMessagesAttachments AS attachments ON (msg.msgID = attachments.msgID) 
    WHERE 
        msg.msgDateCreated < NOW() - INTERVAL %s DAY;
SQL;

        foreach ($connection->fetchAllAssociative(sprintf($sql, $attachmentsLifetime)) as $row) {
            $file = File::getByID($row["fID"]);

            if ($file instanceof \Concrete\Core\Entity\File\File) {
                $file->delete();
            }

            $connection->executeQuery("DELETE FROM UserPrivateMessagesAttachments WHERE msgID = ? AND fID = ?", [$row["msgID"], $row["fID"]]);

            $removedItemsCounter++;
        }

        return t2(
            '%s attachment removed',
            '%s attachments removed',
            $removedItemsCounter
        );
    }
}
