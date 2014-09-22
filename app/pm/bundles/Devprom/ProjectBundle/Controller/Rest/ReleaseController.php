<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\IterationFilterResolver;

class ReleaseController extends RestController
{
	function getEntity()
	{
		return 'Release';
	}
	
	function getFilterResolver()
	{
		return null;
	}
}