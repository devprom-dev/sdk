<?php

namespace Devprom\ServiceDeskBundle\EventListener;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class ServiceDeskExceptionListener {

     public function onKernelException(GetResponseForExceptionEvent $event)
     {
         return;
     }

}