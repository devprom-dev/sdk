<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\ModelServiceTesting;

class TestController extends RestController
{
	function getClassName(Request $request) {
        return 'TestExecution';
    }
}