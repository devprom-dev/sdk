<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\IterationFilterResolver;

class IterationController extends RestController
{
	function getEntity()
	{
		return 'Iteration';
	}
	
	function getFilterResolver()
	{
		return new IterationFilterResolver($this->getRequest()->get('filter'));
	}
}