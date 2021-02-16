<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Tooltip\TooltipProjectService;
use Devprom\ProjectBundle\Service\Tooltip\BrokenTraceExplainService;
use Devprom\ProjectBundle\Service\Tooltip\TooltipObjectListService;


class TooltipController extends Controller
{
    public function showAction(Request $request)
    {
    	if ( $request->get('classname') == "" )
    	{
    		throw $this->createNotFoundException('Class name is undefined');
    	}

    	if ( $request->get('objects') == "" )
    	{
    		throw $this->createNotFoundException('Objects are undefined');
    	}
    	
    	$class = getFactory()->getClass($request->get('classname'));
    	
    	if ( !class_exists($class) )
    	{
    		throw $this->createNotFoundException('Class name doesn\'t exist');
    	}

    	$service = new TooltipProjectService(
    			$request->get('classname'), 
            	$request->get('objects'),
				strpos($request->getQueryString(), 'extended') !== false
		);

        $response = $this->render( 'ProjectBundle:Tooltip:show.html.twig', $service->getData() );
        $response->headers->set('X-Devprom-UI', 'tableonly');
    	return $response;
    }

    public function explainAction(Request $request)
    {
    	if ( $request->get('object') == "" )
    	{
    		throw $this->createNotFoundException('Objects are undefined');
    	}
    	
    	$service = new BrokenTraceExplainService($request->get('object'));

    	return $this->render('ProjectBundle:Tooltip:explain.html.twig', $service->getData());
    }
    
    public function listAction(Request $request)
    {
    	if ( $request->get('classname') == "" )
    	{
    		throw $this->createNotFoundException('Class name is undefined');
    	}

    	if ( $request->get('objects') == "" )
    	{
    		throw $this->createNotFoundException('Objects are undefined');
    	}
    	
    	$class = getFactory()->getClass($request->get('classname'));
    	
    	if ( !class_exists($class) )
    	{
    		throw $this->createNotFoundException('Class name doesn\'t exist');
    	}
    	
    	$service = new TooltipObjectListService(
    			$request->get('classname'), 
            	$request->get('objects')
		);
 	
    	return $this->render( 'ProjectBundle:Tooltip:list.html.twig', $service->getData());
    }

    public function emptyAction(Request $request)
    {
        return new Response();
    }
}