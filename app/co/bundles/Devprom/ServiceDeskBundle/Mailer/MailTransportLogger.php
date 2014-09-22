<?php

namespace Devprom\ServiceDeskBundle\Mailer;
use Monolog\Logger;
use Swift_Plugins_Logger;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class MailTransportLogger implements Swift_Plugins_Logger {

    /** @var  Logger */
    private $logger;

    function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Add a log entry.
     *
     * @param string $entry
     */
    public function add($entry)
    {
        $this->logger->info($entry);
    }

    /**
     * Clear the log contents.
     */
    public function clear()
    {
    }

    /**
     * Get this log as a string.
     *
     * @return string
     */
    public function dump()
    {
    }

}