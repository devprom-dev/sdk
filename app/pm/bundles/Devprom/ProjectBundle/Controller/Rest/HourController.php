<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\TimeFilterResolver;

class HourController extends RestController
{
	function getEntity(Request $request)
	{
		if ( $request->get('class') == 'issues' ) {
			$object = getFactory()->getObject('ActivityRequest');
		}
		else {
			$object = getFactory()->getObject('ActivityTask');
		}

		foreach( array('Iteration', 'OrderNum','Caption') as $field ) {
			$object->addAttributeGroup($field, 'system');
		}
		return $object;
	}
	
    protected function getPostData(Request $request)
	{
		$parms = array();
		if ( $request->get('class') == 'issues' ) {
			$parms['Issue'] = $request->get('object');
		}
		else {
			$parms['Task'] = $request->get('object');
		}
		return array_merge(
			parent::getPostData($request),
			$parms
		);
	}
	
	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
				new TimeFilterResolver(
						$this->getClassName($request), $request->get('object')
				)
			)
		);
	}
}