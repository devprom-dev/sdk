<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\AttachmentFilterResolver;

class AttachmentController extends RestController
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
		return array_merge(
				parent::getPostData($request),
				array (
						'ObjectId' => $request->get('object'),
						'ObjectClass' => $this->getClassName($request)
				)
		);
	}
	
	function getFilterResolver(Request $request)
	{
		return array (
				new AttachmentFilterResolver(
						$this->getClassName($request), $request->get('object')
				)
		);
	}
		
	function getClassName(Request $request)
	{
		switch( $request->get('class') )
		{
			case 'issues':
				return 'request';
			case 'tasks':
				return 'task';
			default:
				return 'dummy';
		}
	}
}