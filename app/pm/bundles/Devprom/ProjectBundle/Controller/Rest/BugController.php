<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Service\Model\ModelServiceBugReporting; 
use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;

class BugController extends RestController
{
	function getEntity()
	{
		return 'Request';
	}

	protected function getFilterResolver()
	{
		return null;
	}
	
    protected function getModelService()
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