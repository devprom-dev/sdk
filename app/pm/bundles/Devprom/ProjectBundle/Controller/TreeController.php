<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\TreeviewModel\FeatureService;
use Devprom\ProjectBundle\Service\TreeviewModel\WikiService;

class TreeController extends Controller
{
    public function indexAction(Request $request)
    {
    	if ( $request->get('classname') == "" ) {
    		throw $this->createNotFoundException('Class name is undefined but required');
    	}

    	if ( in_array(strtolower($request->get('classname')), array('feature','featureterminal')) ) {
    		$service = new FeatureService($request->get('root'));
    	}
    	else {
    		$service = new WikiService($request->get('classname'), $request->get('root'), $request->query->has('cross'));
    	}
    	
    	return new JsonResponse($service->getData());
    }
}