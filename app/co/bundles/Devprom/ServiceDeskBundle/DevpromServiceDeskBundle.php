<?php

namespace Devprom\ServiceDeskBundle;

use Devprom\ServiceDeskBundle\Mailer\MailerLogger;
use Devprom\ServiceDeskBundle\Mailer\MailTransportLogger;
use Devprom\ServiceDeskBundle\Mailer\MessageLogger;
use Swift_Mailer;
use Swift_Plugins_LoggerPlugin;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\Translator;

include_once SERVER_ROOT_PATH."admin/classes/templates/SystemTemplate.php";

class DevpromServiceDeskBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        $this->setUpDevpromTranslations();
        $this->setUpMailLogging();
    }


    public function getParent()
    {
        return "FOSUserBundle";
    }

    protected function setUpDevpromTranslations()
    {
        /** @var Translator $translator */
        $translator = $this->container->get('translator');
        // kkorenkov: I'd rather add these translations under separate domain (not default "messages"), but that complicates
        // issue form rendering - translation for field label is being looked up in the same domain as field values
        $translator->addResource('php', SERVER_ROOT_PATH . "lang/en/resource.php", "en", "messages");
        // kkorenkov: not going to add translations for "ru" because Devprom's vocabulary contains empty values for this locale
        //$translator->addResource('php', SERVER_ROOT_PATH . "lang/ru/resource.php", "ru", "messages");

        // to override branding strings 
        $en_strings = array (
        		\SystemTemplate::getPath().'en/client.en.php' => 'client',
        		\SystemTemplate::getPath().'en/emails.en.php' => 'emails',
        		SERVER_ROOT_PATH . "plugins/dobassist/language/en/array.php" => 'client'
        );
        foreach( $en_strings as $string => $namespace) {
        	if ( file_exists($string) ) $translator->addResource('php', $string, "en", $namespace); 
        }
        $ru_strings = array (
        		\SystemTemplate::getPath().'ru/client.ru.php' => 'client',
        		\SystemTemplate::getPath().'ru/emails.ru.php' => 'emails',
        		SERVER_ROOT_PATH . "plugins/dobassist/language/ru/array.php" => 'client'
        );
        foreach( $ru_strings as $string => $namespace ) {
        	if ( file_exists($string) ) $translator->addResource('php', $string, "ru", $namespace); 
        }
    }

    protected function setUpMailLogging()
    {
        /** @var Swift_Mailer $mailer */
        $mailer = $this->container->get('mailer');
        /** @var MailTransportLogger $logger */
        $logger = $this->container->get('mail_transport_logger');
        /** @var MessageLogger $messageLogger */
        $messageLogger = $this->container->get('message_logger');

        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
        $mailer->registerPlugin($messageLogger);
    }
}
