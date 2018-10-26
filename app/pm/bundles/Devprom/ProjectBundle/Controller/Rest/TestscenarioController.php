<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\TestScenarioFilterResolver;
use Devprom\ProjectBundle\Service\Model\ModelServiceTestingDoc;

class TestscenarioController extends RestController
{
	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
			    new TestScenarioFilterResolver()
			)
		);
	}

	protected function getModelService(Request $request)
	{
		return new ModelServiceTestingDoc(
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
}