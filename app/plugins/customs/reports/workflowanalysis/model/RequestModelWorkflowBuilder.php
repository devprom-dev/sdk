<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/RequestStateDurationPersister.php";

class RequestModelWorkflowBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	
    	$state_it = getFactory()->getObject('IssueState')->getRegistry()->Query(
 				array (
 						new FilterBaseVpdPredicate()
 				)
 		);
    	
    	while( !$state_it->end() )
    	{
			$reference_name = 'State_'.$state_it->getDbSafeReferenceName();
			
			$object->addAttribute( 
					$reference_name, 
					'INTEGER', 
					$state_it->getDisplayName(), 
					true 
			);
			
			$object->addAttributeGroup($reference_name, 'states');
			
    		$state_it->moveNext();
    	}

		$object->addPersister( new RequestStateDurationPersister() );
        $object->addPersister( new StateDurationPersister() );

    	$object->setAttributeOrderNum('Fact', 9999);
    }
}