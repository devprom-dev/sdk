<?php
/**
 * Creates Kernel and Container objects for ServiceDesk in global context so they can be used by Devprom code
 *
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */

global $serviceDeskContainer;

use Devprom\Component\HttpKernel\ServiceDeskAppKernel;

$serviceDeskKernel = ServiceDeskAppKernel::loadWithoutRequest();

$serviceDeskContainer = $serviceDeskKernel->getContainer();

register_shutdown_function(function() use ($serviceDeskKernel) 
{
    $serviceDeskKernel->terminate(new \Symfony\Component\HttpFoundation\Request(),
        new \Symfony\Component\HttpFoundation\Response());
});
