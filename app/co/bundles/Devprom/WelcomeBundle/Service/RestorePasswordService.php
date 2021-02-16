<?php
namespace Devprom\WelcomeBundle\Service;
use Devprom\CommonBundle\Service\Emails\RenderService;

class RestorePasswordService
{
    private $factory;
    private $session;

    function __construct( $factory, $session )
    {
        $this->factory = $factory;
        $this->session = $session;
    }

    function execute( $email )
    {
        $part_it = $this->factory->getObject('cms_User')->getByRef('LCASE(Email)', $email);

        if ( $part_it->getId() < 1) throw new \Exception(text(220));
        if ( $part_it->get('Password') == '' ) throw new \Exception(text(2061));

        $lang = strtolower($this->session->getLanguageUid());
        $renderService = new RenderService(
            $this->session, SERVER_ROOT_PATH."co/bundles/Devprom/CommonBundle/Resources/views/Emails/".$lang
        );

        $body = $renderService->getContent(
            "restore.html.twig",
            array (
                'url' => \EnvironmentSettings::getServerUrl().'/reset?key='.$part_it->getResetPasswordKey()
            )
        );

        $mail = new \HtmlMailbox;
        $mail->appendAddress($part_it->get('Email'));
        $mail->setBody($body);
        $mail->setSubject( text(222) );
        $mail->send();

        return text(223);
    }
}