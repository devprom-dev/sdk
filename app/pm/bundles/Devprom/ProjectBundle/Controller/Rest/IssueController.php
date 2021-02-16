<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\ExecutorFilterResolver;
use Devprom\ProjectBundle\Service\Model\ModelServiceIssue;
use Devprom\ProjectBundle\Service\Model\ModelServiceBugReporting;

class IssueController extends RestController
{
	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
				new ExecutorFilterResolver($request->get('executor'), 'Owner')
			)
		);
	}

    protected function getModelService(Request $request)
    {
    	if ( strpos(var_export($request->request->all(), true), "APP_IID") === false )
		{
			return new ModelServiceIssue(
				new \ModelValidator(
					array (
						new \ModelValidatorObligatory(),
						new \ModelValidatorTypes()
					)
				),
				new \ModelDataTypeMapper(),
				$this->getFilterResolver($request),
                null,
                $request->get('version') != 'v1'
			);
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