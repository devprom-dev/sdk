<?php

namespace Devprom\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\CommonBundle\Service\Widget\ScriptService;

class ScriptsController extends Controller
{
    public function jsAction()
    {
    	$response = new Response();
    	if ( $this->checkNotModified('js', $response) ) return $response; 

    	$service = new ScriptService();
    	$response->setContent($service->getJSBody());
		$response->headers->set('Content-Type', 'text/javascript; charset=utf-8');
    	return $response;
    }

    public function cssAction()
    {
    	$response = new Response();
    	if ( $this->checkNotModified('js', $response) ) return $response; 
    	
    	$service = new ScriptService();
    	$response->setContent($service->getCSSBody());
    	$response->headers->set('Content-Type', 'text/css');
    	return $response;
    }
    
    protected function checkNotModified($method, &$response)
    {
        $hash = md5(get_class($this).$method);
		$response->setEtag($hash);
		
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"'.$hash.'"') 
		{
			$response->setStatusCode(304);
			$response->headers->set('Content-Length', '0');
			return true;
		}
		else
		{ 
			$date = new \DateTime();
			$date->modify('+600 seconds');
			$response->setExpires($date);    	
	    	$response->setMaxAge(600);
	    	$response->headers->set('Pragma', 'public');
	    	return false;
		}
    }
}