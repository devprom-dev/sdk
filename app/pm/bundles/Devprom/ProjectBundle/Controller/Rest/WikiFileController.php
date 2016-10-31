<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\AttachmentFilterResolver;
use Devprom\ProjectBundle\Service\Model\FilterResolver\WikiFileFilterResolver;

class WikiFileController extends AttachmentController
{
	function getEntity(Request $request)
	{
		$object = getFactory()->getObject('WikiPageFile');
		foreach( array('Caption','ContentPath', 'Description','OrderNum') as $field ) {
			$object->addAttributeGroup($field, 'system');
		}
		return $object;
	}
	
    protected function getPostData(Request $request)
	{
		return array (
			'WikiPage' => $request->get('object')
		);
	}
	
	function getFilterResolver(Request $request)
	{
		if ( is_subclass_of($this->getClassName($request), 'WikiPage') ) {
			return array_merge(
				RestController::getFilterResolver($request),
				array (
					new WikiFileFilterResolver(
						$request->get('object')
					)
				)
			);
		}
		else {
			return array_merge(
				RestController::getFilterResolver($request),
				array (
					new AttachmentFilterResolver(
						$this->getClassName($request), $request->get('object')
					)
				)
			);
		}
	}
}