<?php

namespace Devprom\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\CommonBundle\Service\Tooltip\TooltipService;

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
    	
    	if ( getFactory()->getObject($this->getRequest()->get('classname'))->getVpdValue() != '' )
    	{
    		throw $this->createNotFoundException('Access permitted to the entity');
    	}
    	
    	$service = new TooltipService(
    			$this->getRequest()->get('classname'), 
            	$this->getRequest()->get('objects')
		);
 	
    	return $this->render( 'ApplicationBundle:Tooltip:show.html.twig',
            array(
            		'sections' => $service->getData()
    		)
        );
    }
}