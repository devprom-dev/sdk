<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\IterationFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\RequirementFilterResolver;

class RequirementController extends RestController
{
	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
				new RequirementFilterResolver('')
			)
		);
	}
}