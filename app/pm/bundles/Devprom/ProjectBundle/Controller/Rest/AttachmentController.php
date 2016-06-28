<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;

class AttachmentController extends RestController
{
	private $controller = null;

	function buildSpecificController(Request $request) {
		if ( is_subclass_of($this->getClassName($request), 'WikiPage') ) {
			return new WikiFileController();
		}
		else {
			return new AttachmentFileController();
		}
	}

	function getSpecificController(Request $request) {
		if ( !is_object($this->controller) ) $this->controller = $this->buildSpecificController($request);
		return $this->controller;
	}

	function getEntity(Request $request) {
		return $this->getSpecificController($request)->getEntity($request);
	}
	
    protected function getPostData(Request $request) {
		return array_merge(
			parent::getPostData($request),
			$this->getSpecificController($request)->getPostData($request)
		);
	}
	
	function getFilterResolver(Request $request) {
		return array_merge(
			parent::getFilterResolver($request),
			$this->getSpecificController($request)->getFilterResolver($request)
		);
	}
}