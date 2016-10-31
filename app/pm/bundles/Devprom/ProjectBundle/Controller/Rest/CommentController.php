<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\AttachmentFilterResolver;

class CommentController extends RestController
{
	function getEntity(Request $request)
	{
		$object = getFactory()->getObject('Comment');

		foreach( array('ExternalAuthor', 'ObjectId', 'ObjectClass', 'ExternalEmail', 'OrderNum', 'AuthorName', 'AuthorEmail') as $field ) {
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
		return array_merge(
			parent::getFilterResolver($request),
			array (
				new AttachmentFilterResolver(
						$this->getClassName($request), $request->get('object')
				)
			)
		);
	}
}