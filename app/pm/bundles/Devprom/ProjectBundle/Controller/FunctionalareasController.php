<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
namespace Devprom\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\RestBundle\Controller\FOSRestController,
	Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class FunctionalareasController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
    	$service = new WorkspaceService();

    	$workspaces = array_filter(
            $service->getWorkspaces(),
            function($workspace) {
                return $workspace['id'] != 'stg';
            }
        );

        return $this->handleView($this->view($workspaces, 200));
    }
    
    public function putAction(Request $request, $areaId)
    {
    	$workspace = $request->request->all();

    	$service = new WorkspaceService();
		$service->removeWorkspace($areaId);
    	$service->storeWorkspace($workspace);
    		
    	return $this->handleView($this->view($workspace, 200));
	}

    public function patchAction(Request $request, $areaId)
    {
    	$service = new WorkspaceService();
    	$service->removeWorkspace($areaId);
    	
    	$workspace = array_pop(array_filter($service->getWorkspaces(), function($value) use ($areaId) {
    			return $value['id'] == $areaId;
    	}));
    		
    	return $this->handleView($this->view($workspace, 200));
	}
}