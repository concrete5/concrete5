<?php

namespace Concrete\Job;

use Concrete\Core\Job\Job;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserList;

class DeleteInvalidatedUsers extends Job
{
    /**
     * @return string
     */
    public function getJobName()
    {
        return t('Delete Invalidated Users');
    }

    /**
     * @return string
     */
    public function getJobDescription()
    {
        return t('Delete users who never validate their email address long time.');
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function run()
    {
        $config = app('config');
        if ($config->get('concrete.user.registration.type') === 'validate_email') {
            $userList = new UserList();
            $userList->filterByIsValidated(0);
            $threshold = $config->get('concrete.user.registration.validate_email_threshold', 0);
            if ($threshold) {
                $thresholdDateTime = new \DateTime();
                $thresholdDateTime->sub(new \DateInterval('PT' . $threshold . 'S'));
                $userList->filterByDateAdded($thresholdDateTime->format('Y-m-d H:i:s'), '<');
            }
            $users = $userList->getResults();
            $count = 0;
            /** @var UserInfo $user */
            foreach ($users as $user) {
                if ($this->deleteUser($user)) {
                    $count++;
                }
            }

            return t2('%s user deleted.', '%s users deleted.', $count);
        }

        return t('Email validation or user registration is disabled. Job aborted.');
    }

    /**
     * @param UserInfo $userInfo
     *
     * @return bool
     */
    protected function deleteUser(UserInfo $userInfo)
    {
        // We don't want to accidentally delete any users who have activity.
        if ($userInfo->getNumLogins() > 0) {
            return false;
        }

        return $userInfo->delete();
    }
}
