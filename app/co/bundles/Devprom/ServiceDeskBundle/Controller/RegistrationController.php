<?php

namespace Devprom\ServiceDeskBundle\Controller;


use Devprom\ServiceDeskBundle\Mailer\Mailer;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
/**
 * This controller overrides default behavior of FOSUserBundle.
 * todo: replace with EventListener usage introduced in FOSUserBundle 2.x version as soon as 2.x becomes stable
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class RegistrationController extends BaseController {

    public function registerAction()
    {
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            $authUser = false;
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);

            if ($authUser) {
                /** @var Mailer $mailer */
                $mailer = $this->container->get('fos_user.mailer');
                $plainPassword = $this->container->get('session')->get('fos_user_registration/password');
                $mailer->sendRegistrationEmailMessage($user, $plainPassword);

                $this->authenticateUser($user, $response);
            }

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
    }


    public function confirmedAction() {

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $url = $this->container->get('router')->generate('issue_list');

        return new RedirectResponse($url);
    }

}