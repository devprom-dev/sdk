<?php

namespace Devprom\CommonBundle\Doctrine\DBAL\Listener;
 
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\Common\EventSubscriber;
 
class PostConnectListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(Events::postConnect);
    }
 
    public function postConnect(ConnectionEventArgs $args)
    {
        $args->getConnection()->exec("SET time_zone = '".\EnvironmentSettings::getUTCOffset().":00'");
    }
}