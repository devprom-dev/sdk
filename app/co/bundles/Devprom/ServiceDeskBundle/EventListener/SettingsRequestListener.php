<?php

namespace Devprom\ServiceDeskBundle\EventListener;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Routing\Router;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsRequestListener {

    const SETTINGS_PAGE_ROUTE = "settings_dashboard";

    /** @var  Container */
    private $container;

    /** @var  Router */
    private $router;

    function __construct($container, $router)
    {
        $this->router = $router;
        $this->container = $container;
    }

    public function onKernelController(GetResponseForControllerResultEvent $event)
    {
        // skip listener if admin dashboard requested
        if ($event->getRequest()->get('_route') === self::SETTINGS_PAGE_ROUTE ||
            $event->getRequest()->get('_route') === null) {
            return;
        }

        $this->checkSettings($event);
    }

    protected function checkSettings(GetResponseForControllerResultEvent $event) {
        if (count($this->container->getParameter('supportProjects'))<1) {
            $event->setResponse(new RedirectResponse($this->router->generate(self::SETTINGS_PAGE_ROUTE, array(), true)));
        }
    }

}