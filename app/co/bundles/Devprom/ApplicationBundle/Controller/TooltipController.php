<?php

namespace Devprom\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\CommonBundle\Service\Tooltip\TooltipService;

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
    	
    	if ( getFactory()->getObject($request->get('classname'))->getVpdValue() != '' )
    	{
    		throw $this->createNotFoundException('Access permitted to the entity');
    	}
    	
    	$service = new TooltipService(
			$request->get('classname'),
			$request->get('objects')
		);
 	
    	return $this->render( 'ApplicationBundle:Tooltip:show.html.twig',
            array(
            		'sections' => $service->getData()
    		)
        );
    }
}