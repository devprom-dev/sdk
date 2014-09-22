<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
namespace Devprom\ProjectBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\RestBundle\Controller\FOSRestController,
	Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class FunctionalareasController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
    	$service = new WorkspaceService();

        return $this->handleView($this->view($service->getWorkspaces(), 200));
    }
    
    public function putAction($areaId)
    {
    	$workspace = $this->getRequest()->request->all();

    	$service = new WorkspaceService();
    	
    	$service->storeWorkspace($workspace);
    		
    	return $this->handleView($this->view($workspace, 200));
	}

    public function patchAction($areaId)
    {
    	$workspace = $this->getRequest()->request->all();

    	$service = new WorkspaceService();
    	
    	$service->removeWorkspace($areaId);
    	
    	$workspace = array_pop(array_filter($service->getWorkspaces(), function($value) use ($areaId) {
    			return $value['id'] == $areaId;
    	}));
    		
    	return $this->handleView($this->view($workspace, 200));
	}
}