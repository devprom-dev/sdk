<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\StateFilterResolver;

class TaskController extends RestController
{
	function getEntity()
	{
		return 'Task';
	}
	
	function getFilterResolver()
	{
		return array (
				new CommonFilterResolver($this->getRequest()->get('in')),
				new StateFilterResolver($this->getRequest()->get('state'))
		);
	}
}