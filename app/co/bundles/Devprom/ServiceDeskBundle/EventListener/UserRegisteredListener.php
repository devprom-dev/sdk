<?php

namespace Devprom\ServiceDeskBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Devprom\ServiceDeskBundle\Service\ObjectChangeLogger;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class UserRegisteredListener implements EventSubscriberInterface
{
    private $objectChangeLogger;

    public function __construct(ObjectChangeLogger $objectChangeLogger) {
        $this->objectChangeLogger = $objectChangeLogger;
    }

    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event) {
        $user = $event->getUser();
        $event->getRequest()->getSession()->set('fos_user_registration/password', $user->getPlainPassword());
        $this->objectChangeLogger->logExternalUserRegistered($user);
    }
}