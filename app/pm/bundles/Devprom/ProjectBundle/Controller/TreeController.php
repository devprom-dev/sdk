<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\TreeviewModel\FeatureService;
use Devprom\ProjectBundle\Service\TreeviewModel\WikiService;

class TreeController extends Controller
{
    public function indexAction()
    {
    	if ( $this->getRequest()->get('classname') == "" )
    	{
    		throw $this->createNotFoundException('Class name is undefined but required');
    	}

    	if ( in_array(strtolower($this->getRequest()->get('classname')), array('feature','featureterminal')) )
    	{
    		$service = new FeatureService($this->getRequest()->get('root'));
    	}
    	else
    	{
    		$service = new WikiService($this->getRequest()->get('classname'), $this->getRequest()->get('root'));
    	}
    	
    	return new JsonResponse($service->getData());
    }
}