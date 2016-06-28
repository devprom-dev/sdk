<?php
namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Settings\ModulesSettingsService;
use Devprom\ProjectBundle\Service\Settings\NavigationSettingsService;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/settings/ProjectSettingsPage.php";

class SettingsController extends PageController
{
    public function indexAction(Request $request)
    {
    	if ( $request->get('area') == "" )
    	{
    		throw $this->createNotFoundException('Settings area is undefined but required');
    	}

        if ( $request->get('action') == "" )
    	{
    		throw $this->createNotFoundException('Action is undefined but required');
    	}
    	
    	$service = $this->getService($request->get('area'));
    	
    	if ( !is_object($service) ) throw \Exception('Settings service wasn\'t found');
    	
    	switch( $request->get('action') )
    	{
    	    case 'reset':
    	    	$service->reset();
    	    	break;

			case 'resettodefault':
				$service->resetToDefault();
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

	public function projectAction(Request $request) {
		return $this->responsePage( new \ProjectSettingsPage() );
	}
}