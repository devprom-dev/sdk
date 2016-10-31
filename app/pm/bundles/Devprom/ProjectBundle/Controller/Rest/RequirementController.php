<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\IterationFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\RequirementFilterResolver;
use Devprom\ProjectBundle\Service\Model\ModelServiceRequirement;

class RequirementController extends RestController
{
	function getEntity(Request $request)
	{
		return 'Requirement';
	}

	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
				new RequirementFilterResolver('')
			)
		);
	}

	protected function getModelService(Request $request)
	{
		return new ModelServiceRequirement(
			new \ModelValidator(
				array (
					new \ModelValidatorObligatory(),
					new \ModelValidatorTypes()
				)
			),
			new \ModelDataTypeMapper(),
			$this->getFilterResolver($request)
		);
	}
}