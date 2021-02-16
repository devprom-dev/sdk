<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\ModelServiceTesting;

class FeatureController extends RestController
{
	function getClassName(Request $request) {
        return 'Feature';
    }
}