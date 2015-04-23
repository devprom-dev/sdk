<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\StateFilterResolver;

class IssueController extends RestController
{
	function getEntity()
	{
		return 'Request';
	}
	
	function getFilterResolver()
	{
		return array (
				new CommonFilterResolver($this->getRequest()->get('in')),
				new StateFilterResolver($this->getRequest()->get('state'))
		);
	}
}