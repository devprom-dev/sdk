<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\Settings\ModulesSettingsService;
use Devprom\ProjectBundle\Service\Settings\NavigationSettingsService;

class SettingsController extends Controller
{
    public function indexAction()
    {
    	if ( $this->getRequest()->get('area') == "" )
    	{
    		throw $this->createNotFoundException('Settings area is undefined but required');
    	}

        if ( $this->getRequest()->get('action') == "" )
    	{
    		throw $this->createNotFoundException('Action is undefined but required');
    	}
    	
    	$service = $this->getService($this->getRequest()->get('area'));
    	
    	if ( !is_object($service) ) throw \Exception('Settings service wasn\'t found');
    	
    	switch( $this->getRequest()->get('action') )
    	{
    	    case 'reset':
    	    	$service->reset();
    	    	break;

    	    case 'makedefault':
    	    	$service->makeDefault();
    	    	break;
    	    	
    	    default:
    	    	throw \Exception('Corresponding action wasn\'t found');
    	}
    	
    	return new RedirectResponse(getSession()->getApplicationUrl().'profile');
    }
    
    protected function getService( $action )
    {
    	switch($action)
    	{
    	    case 'modules':
    	    	return new ModulesSettingsService();
    	    
    	    case 'menu':
    	    	return new NavigationSettingsService();
    	}
    }
}