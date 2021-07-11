<?php

namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\Notification\UserNotificationEventHandler;
use Concrete\Core\User\Password\PasswordChangeEventHandler;
use Concrete\Core\User\Password\PasswordUsageTracker;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Concrete\Core\User\LogSubscriber as UserLogSubscriber;
use Concrete\Core\User\Group\LogSubscriber as GroupLogSubscriber;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindContainer($this->app);

        // Handle binding events
        $subscribers = [
            $this->app->make(UserLogSubscriber::class),
            $this->app->make(GroupLogSubscriber::class),
        ];
        if ($this->app->resolved(EventDispatcher::class)) {
            $this->bindEvents($this->app->make(EventDispatcher::class), $subscribers);
        } else {
            $this->app->extend(EventDispatcher::class, function (EventDispatcher $director) use ($subscribers) {
                $this->bindEvents($director, $subscribers);
                return $director;
            });
        }

        $this->app->singleton(User::class);
    }

    /**
     * Bind things to the container
     *
     * @param \Concrete\Core\Application\Application $app
     */
    protected function bindContainer(Application $app)
    {
        $this->app->when(PasswordUsageTracker::class)->needs('$maxReuse')->give(function () {
            return $this->app['config']->get('concrete.user.password.reuse.track', 5);
        });

        $this->app->bindShared('user/registration', function () use ($app) {
            return $app->make('Concrete\Core\User\RegistrationService');
        });
        $this->app->bindShared('user/avatar', function () use ($app) {
            return $app->make('Concrete\Core\User\Avatar\AvatarService');
        });
        $this->app->bindShared('user/status', function () use ($app) {
            return $app->make('Concrete\Core\User\StatusService');
        });
        $this->app->bind('Concrete\Core\User\RegistrationServiceInterface', function () use ($app) {
            return $app->make('user/registration');
        });
        $this->app->bind('Concrete\Core\User\StatusServiceInterface', function () use ($app) {
            return $app->make('user/status');
        });
        $this->app->bind('Concrete\Core\User\Avatar\AvatarServiceInterface', function () use ($app) {
            return $app->make('user/avatar');
        });
    }

    protected function bindEvents(EventDispatcherInterface $dispatcher, $subscribers)
    {

        $dispatcher->addListener('on_after_user_deactivate', function ($e) {
            $this->app->call([$this, 'handleEvent'], ['event' => $e]);
        });

        $dispatcher->addListener('on_user_change_password', function ($event) {
            $this->app->make(PasswordChangeEventHandler::class)->handleEvent($event);
        });

        foreach($subscribers as $subscriber) {
            $dispatcher->addSubscriber($subscriber);
        }

    }

    /**
     * Handle routing bound events
     *
     * @param \Symfony\Component\EventDispatcher\Event $event
     * @param \Concrete\Core\User\Notification\UserNotificationEventHandler $service
     *
     * @internal
     */
    public function handleEvent(Event $event, UserNotificationEventHandler $service)
    {
        // If our event is the wrong type, just do a null/void return
        if (!$event instanceof DeactivateUser) {
            return;
        }

        $entity = $event->getUserEntity();

        if ($entity) {
            $service->deactivated($event);
        }
    }
}
