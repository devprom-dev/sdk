<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\StateFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\ExecutorFilterResolver;

class TaskController extends RestController
{
	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
				new CommonFilterResolver($request->get('in')),
				new StateFilterResolver($request->get('state')),
				new ExecutorFilterResolver($request->get('executor'), 'Assignee')
			)
		);
	}
}