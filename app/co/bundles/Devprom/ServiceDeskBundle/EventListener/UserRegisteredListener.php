<?php

namespace Devprom\ServiceDeskBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Devprom\ServiceDeskBundle\Service\ObjectChangeLogger;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Devprom\ServiceDeskBundle\Service\UserService;

class UserRegisteredListener implements EventSubscriberInterface
{
    private $objectChangeLogger;
    private $userService;

    public function __construct(UserService $userService, ObjectChangeLogger $objectChangeLogger) {
        $this->userService = $userService;
        $this->objectChangeLogger = $objectChangeLogger;
    }

    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $event->getRequest()->getSession()->set('fos_user_registration/password', $user->getPlainPassword());

        $this->objectChangeLogger->logExternalUserRegistered($user);
        $this->userService->attachCompanyByDomain($user);
    }
}