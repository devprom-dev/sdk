<?php
namespace Devprom\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class NodesController extends FOSRestController implements ClassResourceInterface
{
	public function cpostAction(Request $request)
    {
    	$report = $request->request->all();

    	$service = new WorkspaceService();
    	
        return $this->handleView($this->view(
        		$service->storeReportToWorkspace(
	        			$report, $request->get('areaid')
				), 200)
		);
    }
}