<?php

namespace Devprom\AdministrativeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallController extends Controller
{
    public function indexAction()
    {
    	global $model_factory;

    	$state = $model_factory->getObject('DeploymentState');
    	
    	if ( $state->IsReadyToBeUsed() )
    	{
    		return new RedirectResponse('/admin/users.php');
    	}
    	
    	ob_start();

    	include SERVER_ROOT_PATH.'admin/views/install/InstallPage.php';
    	
		$page = new \InstallPage;

		$page->render();
		
		$content = ob_get_contents();
		
		ob_end_clean();
		
		return new Response($content);
    }
}