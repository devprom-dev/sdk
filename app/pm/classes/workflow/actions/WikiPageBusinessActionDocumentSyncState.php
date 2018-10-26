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
        $stateAttribute = $object_it->get('StateNameAlt') != '' ? 'StateNameAlt' : 'State';

        $parentObject = getFactory()->getObject(get_class($object_it->object));
        $parentObject->removeNotificator('DocumentStateChangedEventHandler');

		$page_it = $object_it->getParentsIt($parentObject);
		$states = array_unique(
		    array_merge(
		        $page_it->fieldToArray($stateAttribute),
                array(
                    $object_it->get($stateAttribute)
                )
            )
        );
		if ( count($states) == 1 ) return true;

		$objectStateIt = $object_it->getStateIt();
        $service = new WorkflowService($parentObject);
		while( !$page_it->end() ) {
            try {
                $pageStateIt = $page_it->getStateIt();
                if ( $pageStateIt->get('OrderNum') > $objectStateIt->get('OrderNum') ) {
                    $service->moveToState(
                        $page_it, $object_it->get('State'), '', array(), true
                    );
                }
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
