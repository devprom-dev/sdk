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

    function __construct(UserManager $userManager, Mailer $mailer)
    {
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

    protected function getRandomPassword() {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($chars), 0, 5);
        return $password;
    }

}