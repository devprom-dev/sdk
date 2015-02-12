<?php

namespace Devprom\ServiceDeskBundle\Controller;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SecurityController extends BaseController {

    public function loginAction()
    {
        $supportProjects = $this->container->getParameter('supportProjects');
        if (count($supportProjects)<1) {
            $settingsUrl = $this->container->get('router')->generate('settings_dashboard', array(), true);
            return new RedirectResponse($settingsUrl);
        }
        return parent::loginAction();
    }


}