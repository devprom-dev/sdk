<?php

namespace Devprom\ServiceDeskBundle\Service;

use Devprom\ServiceDeskBundle\Mailer\Mailer;
use FOS\UserBundle\Doctrine\UserManager;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class UserService {

    /** @var  Mailer */
    private $mailer;

    /** @var  UserManager */
    private $userManager;
    
    /** @var EntityManager */
    private $em;

    function __construct($em, UserManager $userManager, Mailer $mailer)
    {
    	$this->em = $em;
        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    public function registerUser($email, $name = null) {
        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setUsername($name ? $name : $email);
        $plainPassword = $this->getRandomPassword();
        $user->setPlainPassword($plainPassword);
        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
        $this->userManager->updateUser($user);

        $this->mailer->sendRegistrationEmailMessage($user, $plainPassword);

        return $this->userManager->refreshUser($user);
    }

    public function isCollegues( $email_left, $email_right )
    {
    	return $this->em->getRepository('DevpromServiceDeskBundle:User')->isCollegues($email_left,$email_right);
    }
    
    protected function getRandomPassword() {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($chars), 0, 5);
        return $password;
    }

}