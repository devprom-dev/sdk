<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageComparableSnapshot.php";

class OpenBrokenTraceWebMethod extends WebMethod
{
	public function execute_request()
	{
		if ( $_REQUEST['object'] < 1 ) throw new Exception('Object identifier is required');
		
		$page_it = getFactory()->getObject('WikiPage')->getExact($_REQUEST['object']);

		$trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('TargetPage', $page_it->getId()),
						new FilterAttributePredicate('IsActual', 'N')
				)
		);

		if ( $trace_it->getId() < 1 ) throw new Exception('No broken trace was found');
		if ( $trace_it->get('Type') == 'branch' )
		{
			$type_it = getFactory()->getObject('WikiType')->getExact($page_it->get('ReferenceName'));

			$broken = getFactory()->getObject($type_it->get('ClassName'));
			$broken_it = $broken->createCachedIterator(array($page_it->getData()));

			if ( $trace_it->get('UnsyncReasonType') == 'text-changed' )
			{
				$url = $broken_it->getViewUrl().'&linkstate=nonactual';
				foreach( $broken->getAttributesByGroup('source-attribute') as $attribute ) {
					if ( $broken->getAttributeClass($attribute) == get_class($broken) ) {
						$url .= '&hide=all&show=Content-'.$attribute;
					}
				}
			}
			else {
				$url = $broken_it->getViewUrl().'&compareto=document:'.$trace_it->get('SourceDocumentId');
			}

			echo $url;
		}
		else
		{
			$type = getFactory()->getObject('WikiType');

			$source_type_it = $type->getExact($trace_it->get('SourcePageReferenceName'));
			$target_type_it = $type->getExact($trace_it->get('TargetPageReferenceName'));

			$target = getFactory()->getObject($target_type_it->get('ClassName'));
			$target_it = $target->getExact($trace_it->get('TargetPage'));

			$url = $target_it->getViewUrl().'&linkstate=nonactual';

			foreach( $target->getAttributesByGroup('source-attribute') as $attribute ) {
				if ( $target->getAttributeClass($attribute) == $source_type_it->get('ClassName') ) {
					$url .= '&hide=all&show=Content-'.$attribute;
				}
			}
			
			echo $url;
		}
	}
}
