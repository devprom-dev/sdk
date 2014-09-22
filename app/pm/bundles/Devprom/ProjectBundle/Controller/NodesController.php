<?php
namespace Devprom\ProjectBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class NodesController extends FOSRestController implements ClassResourceInterface
{
	public function cpostAction()
    {
    	$report = $this->getRequest()->request->all();

    	$service = new WorkspaceService();
    	
        return $this->handleView($this->view(
        		$service->storeReportToWorkspace(
	        			$report, $this->getRequest()->get('areaid')
				), 200)
		);
    }
}