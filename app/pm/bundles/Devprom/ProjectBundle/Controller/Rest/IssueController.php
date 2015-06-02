<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\StateFilterResolver;
use Devprom\ProjectBundle\Service\Model\ModelServiceBugReporting; 

class IssueController extends RestController
{
	function getEntity()
	{
		return 'Request';
	}
	
	function getFilterResolver()
	{
		return array (
				new CommonFilterResolver($this->getRequest()->get('in')),
				new StateFilterResolver($this->getRequest()->get('state'))
		);
	}

    protected function getModelService()
    {
    	if ( strpos(var_export($this->getRequest()->request->all(), true), "APP_IID") === false ) {
    		return parent::getModelService();
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