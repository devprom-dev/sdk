<?php
namespace Devprom\ServiceDeskBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ServiceDeskBundle\Service\UserService;

class RegistrationConfirmListener implements EventSubscriberInterface
{
    private $router;
    private $userService;

    public function __construct(UrlGeneratorInterface $router, UserService $userService) {
        $this->router = $router;
        $this->userService = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRM => 'onRegistrationConfirm'
        );
    }

    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        $this->userService->attachCompanyByDomain($event->getUser());
        $url = $this->router->generate('issue_list');
        $event->setResponse(new RedirectResponse($url));
    }
}