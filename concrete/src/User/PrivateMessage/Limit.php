<?php

namespace Concrete\Core\User\PrivateMessage;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Event\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Limit
{
    /**
     * @var bool Tracks whether limiting is enabled
     */
    protected static $enabled = true;

    /**
     * checks to see if a user has exceeded their limit for sending private messages.
     *
     * @param int $uID
     *
     * @return bool
     */
    public static function isOverLimit($uID)
    {
        $app = Application::getFacadeApplication();

        if ($app['config']->get('concrete.user.private_messages.throttle_max') === 0) {
            return false;
        }
        if ($app['config']->get('concrete.user.private_messages.throttle_max_timespan') === 0) {
            return false;
        }

        $db = $app->make(Connection::class);
        $dt = new DateTime();
        $dt->modify('-' . $app['config']->get('concrete.user.private_messages.throttle_max_timespan') . ' minutes');
        $v = [$uID, $dt->format('Y-m-d H:i:s')];
        $q = 'SELECT COUNT(msgID) as sent_count FROM UserPrivateMessages WHERE uAuthorID = ? AND msgDateCreated >= ?';
        $count = $db->fetchColumn($q, $v);

        if ($count > $app['config']->get('concrete.user.private_messages.throttle_max')) {
            self::notifyAdmin($uID);

            return true;
        }

        return false;
    }

    public static function getErrorObject()
    {
        $app = Application::getFacadeApplication();
        $ve = $app->make('helper/validation/error');
        $ve->add(t('You may not send more than %s messages in %s minutes', $app['config']->get('concrete.user.private_messages.throttle_max'), $app['config']->get('concrete.user.private_messages.throttle_max_timespan')));

        return $ve;
    }

    /**
     * Enable or disable Limits.
     *
     * @param bool $enabled
     */
    public static function setEnabled($enabled = true)
    {
        static::$enabled = (bool) $enabled;
    }

    protected static function notifyAdmin($offenderID)
    {
        $app = Application::getFacadeApplication();
        /** @var UserInfoRepository $repository */
        $repository = $app->make(UserInfoRepository::class);
        $offender = $repository->getByID($offenderID);
        if ($offender) {
            $ue = new UserInfo($offender);
            $app->make(EventDispatcherInterface::class)->dispatch('on_private_message_over_limit', $ue);

            $admin = $repository->getByID(USER_SUPER_ID);

            $logger = $app->make('log/factory')->createLogger(Channels::CHANNEL_SPAM);
            $logger->warning(t('User: %s has tried to send more than %s private messages within %s minutes',
                $offender->getUserName(),
                $app['config']->get('concrete.user.private_messages.throttle_max'),
                $app['config']->get('concrete.user.private_messages.throttle_max_timespan')));

            $mh = $app->make('mail');

            $mh->addParameter('offenderUname', $offender->getUserName());
            $mh->addParameter('profileURL', $offender->getUserPublicProfileUrl());
            $mh->addParameter('profilePreferencesURL', $app->make('url/manager')->resolve(['/account/edit_profile']));

            $mh->to($admin->getUserEmail());
            $mh->addParameter('siteName', tc('SiteName', $app->make('site')->getSite()->getSiteName()));
            $mh->load('private_message_admin_warning');
            $mh->sendMail();
        }
    }
}
