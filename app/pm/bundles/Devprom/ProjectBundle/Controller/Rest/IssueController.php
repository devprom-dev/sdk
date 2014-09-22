<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;

class IssueController extends RestController
{
	function getEntity()
	{
		return 'Request';
	}
	
	function getFilterResolver()
	{
		return null;
	}
}