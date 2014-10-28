<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Tooltip\TooltipProjectService;
use Devprom\ProjectBundle\Service\Tooltip\BrokenTraceExplainService;
use Devprom\ProjectBundle\Service\Tooltip\TooltipObjectListService;

class TooltipController extends Controller
{
    public function showAction()
    {
    	if ( $this->getRequest()->get('classname') == "" )
    	{
    		throw $this->createNotFoundException('Class name is undefined');
    	}

    	if ( $this->getRequest()->get('objects') == "" )
    	{
    		throw $this->createNotFoundException('Objects are undefined');
    	}
    	
    	$class = getFactory()->getClass($this->getRequest()->get('classname'));
    	
    	if ( !class_exists($class) )
    	{
    		throw $this->createNotFoundException('Class name doesn\'t exist');
    	}
    	
    	$service = new TooltipProjectService(
    			$this->getRequest()->get('classname'), 
            	$this->getRequest()->get('objects'),
            	$this->getRequest()->get('baseline')
		);
 	
    	return $this->render( 'ProjectBundle:Tooltip:show.html.twig', $service->getData() );
    }

    public function explainAction()
    {
    	if ( $this->getRequest()->get('object') == "" )
    	{
    		throw $this->createNotFoundException('Objects are undefined');
    	}
    	
    	$service = new BrokenTraceExplainService($this->getRequest()->get('object'));

    	return $this->render('ProjectBundle:Tooltip:explain.html.twig', $service->getData());
    }
    
    public function listAction()
    {
    	if ( $this->getRequest()->get('classname') == "" )
    	{
    		throw $this->createNotFoundException('Class name is undefined');
    	}

    	if ( $this->getRequest()->get('objects') == "" )
    	{
    		throw $this->createNotFoundException('Objects are undefined');
    	}
    	
    	$class = getFactory()->getClass($this->getRequest()->get('classname'));
    	
    	if ( !class_exists($class) )
    	{
    		throw $this->createNotFoundException('Class name doesn\'t exist');
    	}
    	
    	$service = new TooltipObjectListService(
    			$this->getRequest()->get('classname'), 
            	$this->getRequest()->get('objects')
		);
 	
    	return $this->render( 'ProjectBundle:Tooltip:list.html.twig', $service->getData());
    }
}