<?php

namespace Devprom\ServiceDeskBundle\EventListener;
use Devprom\ServiceDeskBundle\Security\LicenseChecker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Twig_Environment;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class RequestListener {

    /** @var SecurityContext  */
    protected $securityContext;

    /** @var  Twig_Environment */
    protected $twig;

    public function __construct(SecurityContext $securityContext, Twig_Environment $twig)
    {
        $this->securityContext = $securityContext;
        $this->twig = $twig;
    }

    public function onKernelController(GetResponseEvent $event)
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
        $request->setLocale($request->getSession()->get("_locale"));
    }

    /**
     * @return mixed
     */
    protected function getUserLanguage()
    {
        $token = $this->securityContext->getToken();
        if ($token && is_object($user = $token->getUser())) {
            return $token->getUser()->getLanguage();
        }
        return null;
    }

}