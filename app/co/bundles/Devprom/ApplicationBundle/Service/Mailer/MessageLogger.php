<?php
namespace Devprom\ApplicationBundle\Service\Mailer;

use DateTime;
use PhpImap\Mailbox as ImapMailbox;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;

class MessageLogger implements Swift_Events_SendListener
{
    protected $logger;

    function __construct($logger) {
        $this->logger = $logger;
    }

    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $this->logger->info(
            sprintf("Sending email message:\nDate: %s\nTo: %s\nFrom: %s\nSubject: %s",
                date(DateTime::RFC822, $message->getDate()),
                join(",", array_keys($message->getTo())),
                join(",", array_keys($message->getFrom())),
                $message->getSubject()
            )
        );
    }

    public function sendPerformed(Swift_Events_SendEvent $evt) {
        $this->logger->info(sprintf("Result: %d", $evt->getResult()));
        foreach( $evt->getFailedRecipients() as $email ) {
            $this->logger->info(sprintf("Failed recipient: %s", $email));
        }
    }
}