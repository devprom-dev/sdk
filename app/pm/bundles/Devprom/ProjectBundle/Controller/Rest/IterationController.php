<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\IterationFilterResolver;

class IterationController extends RestController
{
	function getEntity(Request $request)
	{
		return 'Iteration';
	}

	function getFilterResolver(Request $request)
	{
		return array (
				new IterationFilterResolver($request->get('filter'))
		);
	}
}