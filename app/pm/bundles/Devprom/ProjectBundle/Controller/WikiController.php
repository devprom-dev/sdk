<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\Wiki\SelectService;

class WikiController extends Controller
{
    public function selectAction()
    {
    	if ( $this->getRequest()->get('classname') == "" )
    	{
    		throw $this->createNotFoundException('Class name is undefined but required');
    	}

    	$service = new SelectService($this->getRequest()->get('classname'), $this->getRequest()->get('root'));
    	
    	return new JsonResponse($service->getData());
    }
}