<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\ModelServiceTesting;

class TestitemController extends RestController
{
	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
			)
		);
	}

	protected function getModelService(Request $request)
	{
		return new ModelServiceTesting(
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

	function getClassName(Request $request) {
        return 'TestCaseExecution';
    }
}