<?php

namespace Devprom\ServiceDeskBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Model\UserInterface;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class ResettingController extends BaseController
{

    /**
     * This action overrides default behavior of FOSUserbundle.
     * todo: replace with EventListener usage introduced in FOSUserBundle 2.x version as soon as 2.x becomes stable
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('issue_list');
    }

    protected function getObfuscatedEmail(UserInterface $user)
    {
        return $user->getEmail();
    }

}