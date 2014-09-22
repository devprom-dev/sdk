<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\AdministrativeBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LicenseController extends BaseController
{
    public function indexAction()
    {
    	if ( is_object($response = $this->checkAccess()) ) return $response;
    	
    	ob_start();

    	include SERVER_ROOT_PATH.'admin/views/LicensePage.php';
    	
		$page = new \LicensePage;

		$page->render();
		
		$content = ob_get_contents();
		
		ob_end_clean();
		
		return new Response($content);
    }
}