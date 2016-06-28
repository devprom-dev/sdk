<?php
namespace Devprom\ProjectBundle\Service\Network;

abstract class NetworkServiceModelBuilder
{
    abstract public function build( \SessionBase $session, NetworkService $service );
}