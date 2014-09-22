<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\Widget\ScriptService;

class ScriptsController extends Controller
{
    public function jsAction()
    {
    	$service = new ScriptService();

    	$response = new Response($service->getJSBody());
    	
		$response->headers->set('Content-Type', 'text/javascript; charset=utf-8');
    	
    	return $response;
    }

    public function cssAction()
    {
    	$service = new ScriptService();

    	$response = new Response($service->getCSSBody());
    	
		$response->headers->set('Content-Type', 'text/css');
    	
    	return $response;
    }
}