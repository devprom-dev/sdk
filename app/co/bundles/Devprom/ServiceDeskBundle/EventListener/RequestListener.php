<?php

namespace Devprom\ServiceDeskBundle\EventListener;
use Devprom\ServiceDeskBundle\Security\LicenseChecker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig_Environment;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class RequestListener implements EventSubscriberInterface
{
    /** @var TokenStorage  */
    protected $tokenStorage;
    /** @var  Twig_Environment */
    protected $twig;
    protected $defaultLocale;

    public function __construct(TokenStorage $tokenStorage, Twig_Environment $twig, $defaultLocale = 'en')
    {
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->checkLicense($event);
        $this->setRequestAndSessionLocale($event);
    }

    protected function checkLicense(GetResponseEvent $event) {
        global $plugins;
        $licenseChecker = new LicenseChecker($plugins);
        if (!$licenseChecker->isValid()) {
            $event->setResponse(new Response($this->twig->render('DevpromServiceDeskBundle:Exception:not_licensed.html.twig')));
        }
    }

    /**
     * @param GetResponseEvent $event
     */
    protected function setRequestAndSessionLocale(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $lang = $this->getUserLanguage();
        if ($lang) {
            $request->getSession()->set("_locale", $lang);
        }
        if ($request->getSession()->get("_locale") != '' ) {
        	$request->setLocale($request->getSession()->get("_locale"));
        }
}

/**
 * @return mixed
 */
protected function getUserLanguage()
{
    $token = $this->tokenStorage->getToken();
    if ($token && is_object($user = $token->getUser())) {
        return $token->getUser()->getLanguage();
    }
    return null;
}

static public function getSubscribedEvents()
{
    return array(
        // must be registered before the default Locale listener
        KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
    );
}
}