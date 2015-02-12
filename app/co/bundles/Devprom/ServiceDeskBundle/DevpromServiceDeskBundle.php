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

class DevpromServiceDeskBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        // ожидаем, что строки будут в первую очередь в cp1251. —м. [I-4482] и FOS\UserBundle\Util\Canonicalizer
        if (array_search("Windows-1251", mb_detect_order()) === false) {
            mb_detect_order("windows-1251," . join(",", mb_detect_order()));
        }

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
