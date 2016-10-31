<?php
namespace Devprom\ApplicationBundle\Service\Mailer;

use Monolog\Logger;
use Swift_Plugins_Logger;

class MailTransportLogger implements Swift_Plugins_Logger
{
    private $logger;

    function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function add($entry) {
        $this->logger->info($entry);
    }

    public function clear() {
    }

    public function dump() {
    }
}