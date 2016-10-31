<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class WikiPageBusinessActionDocumentSyncState extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '633bcf08-b935-11e4-a71e-12e3f512a338';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('ParentPage') == '' ) return true;
		if ( $object_it->get('DocumentId') == '' ) return true;

		$page_it = $object_it->object->getRegistry()->Query(
				array (
						new WikiRootTransitiveFilter($object_it->get('DocumentId')),
						new WikiNonRootFilter()
				)
		);
		$children_states = array_unique($page_it->fieldToArray('State'));
		if ( count($children_states) != 1 ) return true;

		$state = array_shift($children_states);
		$root_it = $object_it->getRootIt();
		if ( $root_it->get('State') == $state ) return true;
				
		$service = new WorkflowService($object_it->object);
		try {
			$service->moveToState(
						$root_it, $state, '', array(), false
				);
		}
 		catch( Exception $e ) {
			Logger::getLogger('System')->error($e->getMessage());
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
