<?php

namespace Devprom\ServiceDeskBundle\Mailer;
use DateTime;
use ImapMailbox;
use Monolog\Logger;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class MessageLogger implements Swift_Events_SendListener {

    /** @var  Logger */
    protected $logger;

    function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $this->logger->info(
            sprintf("Sending email message:\nDate: %s\nTo: %s\nFrom: %s\nSubject: %s",
                date(DateTime::RFC822, $message->getDate()),
                join(",", array_keys($message->getTo())),
                join(",", array_keys($message->getFrom())),
                ImapMailbox::decodeMimeStr($message->getSubject(), APP_ENCODING)
            )
        );
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $this->logger->info(sprintf("Result: %d, failed recipients: %s", $evt->getResult(), join(",", $evt->getFailedRecipients())));
    }

}