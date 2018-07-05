<?php

namespace Devprom\ServiceDeskBundle\Service;

use Devprom\ServiceDeskBundle\Mailer\Mailer;
use FOS\UserBundle\Doctrine\UserManager;
use PhpImap\Exception;

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

    /** @var  ObjectChangeLogger */
    private $objectChangeLogger;

    function __construct($em, UserManager $userManager, Mailer $mailer, ObjectChangeLogger $objectChangeLogger)
    {
    	$this->em = $em;
        $this->mailer = $mailer;
        $this->userManager = $userManager;
        $this->objectChangeLogger = $objectChangeLogger;
    }

    public function registerUser($email, $name = null, $password = '')
    {
        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setUsername($name ? $name : $email);
        $plainPassword = $password != '' ? $password : \TextUtils::getRandomPassword();
        $user->setPlainPassword($plainPassword);
        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
        $this->userManager->updateUser($user);

        $this->mailer->sendRegistrationEmailMessage($user, $plainPassword);
        $this->objectChangeLogger->logExternalUserRegistered($user);

        return $this->userManager->refreshUser($user);
    }

    public function setPassword($email, $password)
    {
        $user = $this->userManager->findUserByEmail($email);
        if ( !is_object($user) ) throw new \Exception('There is no a user with email given');

        $user->setPlainPassword($password);
        $this->userManager->updatePassword($user);
        $this->userManager->updateUser($user);
    }

    public function isCollegues( $email_left, $email_right )
    {
    	return $this->em->getRepository('DevpromServiceDeskBundle:User')->isCollegues($email_left,$email_right);
    }
}