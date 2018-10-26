<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class WikiPageBusinessActionChildrenSyncState extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '57a8ce52-b930-11e4-a71e-12e3f512a338';
 	}
	
	function apply( $object_it )
 	{
 	    $childObject = getFactory()->getObject(get_class($object_it->object));
        $childObject->removeNotificator('DocumentStateChangedEventHandler');
        $stateAttribute = $object_it->get('StateNameAlt') != '' ? 'StateNameAlt' : 'State';

		$page_it = $childObject->getRegistry()->Query(
            array (
                new ParentTransitiveFilter($object_it->getId()),
                new FilterNotInPredicate($object_it->getId()),
                new WikiNonRootFilter()
            )
		);
		
		$service = new WorkflowService($childObject);
		while( !$page_it->end() )
		{
		    if ( $page_it->get($stateAttribute) == $object_it->get($stateAttribute) ) {
                $page_it->moveNext();
                continue;
            }
			try {
				$service->moveByTransition(
						$page_it, $object_it->getRef('LastTransition'), '', array(), true
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
