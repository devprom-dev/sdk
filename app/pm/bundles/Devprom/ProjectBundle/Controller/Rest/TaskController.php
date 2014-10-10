<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;

class TaskController extends RestController
{
	function getEntity()
	{
		return 'Task';
	}
	
	function getFilterResolver()
	{
		return new CommonFilterResolver($this->getRequest()->get('in'));
	}
}