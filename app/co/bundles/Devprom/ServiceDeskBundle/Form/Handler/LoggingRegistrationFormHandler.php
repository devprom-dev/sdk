<?php

namespace Devprom\ServiceDeskBundle\Form\Handler;

use Devprom\ServiceDeskBundle\Service\ObjectChangeLogger;
use FOS\UserBundle\Form\Handler\RegistrationFormHandler;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use ModelFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class LoggingRegistrationFormHandler extends RegistrationFormHandler
{

    /**
     * @var ObjectChangeLogger
     */
    private $objectChangeLogger;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager,
                                MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator,
                                ObjectChangeLogger $objectStateLogger, ModelFactory $modelFactory, ContainerInterface $container)
    {
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
        $this->objectChangeLogger = $objectStateLogger;
        $this->modelFactory = $modelFactory;
        $this->container = $container;
    }


    protected function onSuccess(UserInterface $user, $confirmation)
    {
        $this->container->get('session')->set('fos_user_registration/password', $user->getPlainPassword());
        parent::onSuccess($user, $confirmation);
        $vpd = \ModelProjectOriginationService::getOrigin($this->container->getParameter('supportProjectId'));
        $this->objectChangeLogger->logExternalUserRegistered($user, $vpd);
    }


}