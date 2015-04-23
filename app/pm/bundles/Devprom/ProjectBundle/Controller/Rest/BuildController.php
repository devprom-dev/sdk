<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;

class BuildController extends RestController
{
	function getEntity()
	{
		return 'Build';
	}
	
	function getFilterResolver()
	{
		return array();
	}
}