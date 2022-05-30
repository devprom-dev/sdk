<?php
namespace Devprom\ServiceDeskBundle\EventListener;

use Devprom\ServiceDeskBundle\Service\ObjectChangeLogger;
use Devprom\ServiceDeskBundle\Service\UserService;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLoggedListener implements EventSubscriberInterface
{
    private $objectChangeLogger;
    private $userService;

    public function __construct(UserService $userService, ObjectChangeLogger $objectChangeLogger) {
        $this->userService = $userService;
        $this->objectChangeLogger = $objectChangeLogger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    /**
     * @param UserEvent $event
     */
    public function onImplicitLogin(UserEvent $event)
    {
        $user = $event->getUser();
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ( !is_object($user->getCompany()) ) {
            $this->userService->attachCompanyByDomain($user);
        }
    }
}
