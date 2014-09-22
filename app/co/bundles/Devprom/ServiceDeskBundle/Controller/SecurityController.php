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
        $supportProjectId = $this->container->getParameter('supportProjectId');
        if (!$supportProjectId) {
            $settingsUrl = $this->container->get('router')->generate('settings_dashboard', array(), true);
            return new RedirectResponse($settingsUrl);
        }
        return parent::loginAction();
    }


}