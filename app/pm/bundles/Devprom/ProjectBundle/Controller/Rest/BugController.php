<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Service\Model\ModelServiceBugReporting; 
use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;

class BugController extends RestController
{
	function getEntity(Request $request)
	{
		return 'Bug';
	}

    protected function getModelService(Request $request)
    {
    	return new ModelServiceBugReporting(
            new \ModelValidator(
                    array (
                            new \ModelValidatorObligatory(),
                            new \ModelValidatorTypes()
                    )
            ),
            new \ModelDataTypeMapper(),
            null,
            null,
            $request->get('version') != 'v1'
		);
    }

    protected function getPostData(Request $request)
    {
        $data = $request->request->all();
        if ( trim($data['Caption']) == '' ) return array();
        return $data;
    }
}