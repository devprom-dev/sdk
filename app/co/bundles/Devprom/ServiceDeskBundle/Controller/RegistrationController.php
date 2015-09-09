<?php

namespace Devprom\ServiceDeskBundle\Controller;

use Devprom\ServiceDeskBundle\Mailer\Mailer;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Devprom\ServiceDeskBundle\Service\ObjectChangeLogger;

/**
/**
 * This controller overrides default behavior of FOSUserBundle.
 * todo: replace with EventListener usage introduced in FOSUserBundle 2.x version as soon as 2.x becomes stable
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class RegistrationController extends BaseController {

    public function confirmedAction() {

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $url = $this->container->get('router')->generate('issue_list');

        return new RedirectResponse($url);
    }

}