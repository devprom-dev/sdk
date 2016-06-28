<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;

class BuildController extends RestController
{
	function getEntity(Request $request)
	{
		return 'Build';
	}
}