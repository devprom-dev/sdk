<?php
namespace Devprom\WelcomeBundle\Service;

class RestorePasswordService
{
    private $factory;
    private $controller;
    private $emailTemplate;
    private $session;

    function __construct( $factory, $controller, $session )
    {
        $this->factory = $factory;
        $this->controller = $controller;
        $this->session = $session;

        $lang = strtolower($this->session->getLanguageUid());
        $this->emailTemplate = 'CommonBundle:Emails/'.$lang.':restore.html.twig';
    }

    function execute( $email )
    {
        $part_it = $this->factory->getObject('cms_User')->getByRef('LCASE(Email)', $email);

        if ( $part_it->getId() < 1) throw new \Exception(text(220));
        if ( $part_it->get('Password') == '' ) throw new \Exception(text(2061));

        $settings_it = $this->factory->getObject('cms_SystemSettings')->getAll();

        $body = $this->controller->render( $this->emailTemplate,
                        array (
                            'url' => \EnvironmentSettings::getServerUrl().'/reset?key='.$part_it->getResetPasswordKey()
                        )
                    )->getContent();

        $mail = new \HtmlMailbox;
        $mail->appendAddress($part_it->get('Email'));
        $mail->setBody($body);
        $mail->setSubject( text(222) );
        $mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
        $mail->send();

        return text(223);
    }
}