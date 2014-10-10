<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\CommonFilterResolver;

class IssueController extends RestController
{
	function getEntity()
	{
		return 'Request';
	}
	
	function getFilterResolver()
	{
		return new CommonFilterResolver($this->getRequest()->get('in'));
	}
}