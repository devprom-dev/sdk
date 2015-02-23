<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once "BusinessAction.php";

class WikiPageBusinessActionChildrenSyncState extends BusinessAction
{
 	function getId()
 	{
 		return '57a8ce52-b930-11e4-a71e-12e3f512a338';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('ParentPage') != '' ) return true;

		$page_it = $object_it->object->getRegistry()->Query(
				array (
						new WikiRootTransitiveFilter($object_it->getId()),
						new WikiNonRootFilter()
				)
		);
		
		$service = new WorkflowService($object_it->object);
		while( !$page_it->end() )
		{
			try {
				$service->moveToState(
						$page_it, $object_it->get('State'), $object_it->get('TransitionComment'), array(), false
					);
			}
			catch( Exception $e ) {
				Logger::getLogger('System')->error($e->getMessage());
			}
			$page_it->moveNext();
		}
 		
 		return true;
 	}

 	function getObject()
 	{
 		return null;
 	}
 	
 	function getDisplayName()
 	{
 		return '';
 	}
}
