<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\AttachmentFilterResolver;

class AttachmentFileController extends AttachmentController
{
	function getEntity(Request $request)
	{
		$object = getFactory()->getObject('Attachment');
		foreach( array('FilePath', 'ObjectId', 'ObjectClass', 'Description') as $field ) {
			$object->addAttributeGroup($field, 'system');
		}
		return $object;
	}
	
    protected function getPostData(Request $request)
	{
		return array (
			'ObjectId' => $request->get('object'),
			'ObjectClass' => $this->getClassName($request)
		);
	}
	
	function getFilterResolver(Request $request)
	{
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