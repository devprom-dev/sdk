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
			
			$broken_it = getFactory()->getObject($type_it->get('ClassName'))->createCachedIterator(array($page_it->getData()));
			
			$snapshot = new WikiPageComparableSnapshot($broken_it->getRootIt());
	 		
	 		$snapshot_it = $snapshot->getAll();
			
	 		$snapshot_it->moveTo('ObjectId', $trace_it->get('SourceDocumentId'));
	 		
	 		if ( $snapshot_it->getId() > 0 )
	 		{
				echo $broken_it->getViewUrl().'&compareto='.$snapshot_it->getId();
	 		}
	 		else
	 		{
				echo $broken_it->getViewUrl().'&compareto=document:'.$trace_it->get('SourceDocumentId');
	 		}
		}
		else
		{
			$type_it = getFactory()->getObject('WikiType')->getExact($trace_it->get('SourcePageReferenceName'));
			
			$broken_it = getFactory()->getObject($type_it->get('ClassName'))->getExact($trace_it->get('SourcePage'));
			
			$change_it = getFactory()->getObject('WikiPageChange')->getRegistry()->Query(
				array (
					new FilterAttributePredicate('WikiPage', $page_it->getId()),
					new SortRecentClause()
				)
			);
			
			$url = $broken_it->getHistoryUrl();
			if ( $change_it->getId() != '' ) {
				$url .= '&start='.$change_it->getDateTimeFormat('RecordCreated');  
			}
			echo $url;
		}
	}
}
