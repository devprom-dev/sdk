<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\StateFilterResolver;
use Devprom\ProjectBundle\Service\Model\ModelServiceBugReporting; 

class IssueController extends RestController
{
	function getEntity(Request $request)
	{
		return 'Request';
	}
	
	function getFilterResolver(Request $request)
	{
		return array (
				new CommonFilterResolver($request->get('in')),
				new StateFilterResolver($request->get('state'))
		);
	}

    protected function getModelService(Request $request)
    {
    	if ( strpos(var_export($request->request->all(), true), "APP_IID") === false ) {
    		return parent::getModelService($request);
    	}
    	else {
    		return $this->getBugsModelService();
    	}
    }
    
	protected function getBugsModelService()
    {
    	return new ModelServiceBugReporting(
    			new \ModelValidator(
						array (
								new \ModelValidatorObligatory(),
								new \ModelValidatorTypes()
    					)
				), 
    			new \ModelDataTypeMapper(),
    			null
		);
    }
}