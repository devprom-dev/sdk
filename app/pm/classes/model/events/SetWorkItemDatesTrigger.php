<?php

class SetWorkItemDatesTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !in_array($object_it->object->getEntityRefName(), array('pm_Task', 'pm_ChangeRequest')) ) return;
	    if ( !in_array($kind, array(TRIGGER_ACTION_ADD, TRIGGER_ACTION_MODIFY)) ) return;

	    $this->processStartDate( $object_it, $content );

	    if ( array_key_exists('State', $content) ) {
            $this->processFinishDate($object_it);
	    }
	}
	
	function processFinishDate( $object_it )
	{
		$value = in_array($object_it->get('State'), $object_it->object->getTerminalStates()) ? 'NOW()' : "NULL";
	    $table_name = $object_it->object->getEntityRefName();

	    if ( $value == 'NULL' ) {
            DAL::Instance()->Query(
                " UPDATE ".$table_name." SET FinishDate = ".$value." WHERE ".$table_name."Id = ".$object_it->getId()
            );
        }
        else {
            DAL::Instance()->Query(
                " UPDATE ".$table_name." SET FinishDate = ".$value." WHERE FinishDate IS NULL AND ".$table_name."Id = ".$object_it->getId()
            );
        }
        DAL::Instance()->Query(
            " UPDATE ".$table_name." SET StartDate = FinishDate WHERE StartDate IS NULL AND FinishDate IS NOT NULL AND ".$table_name."Id = ".$object_it->getId()
        );
	}

	function processStartDate( $object_it, $content )
	{
	    $table_name = $object_it->object->getEntityRefName();
        $value = '';

		// when the state is changed
		if ( array_key_exists('State', $content) )
		{
		    $stateIt = \WorkflowScheme::Instance()->getStateIt($object_it);
            while( !$stateIt->end() ) {
                if ( $object_it->get('State') == $stateIt->get('ReferenceName') ) {
                    if ( $stateIt->get('IsTerminal') == 'N' ) {
                        // submitted
                        $value = "NULL";
                        break;
                    }
                    if ( $stateIt->get('IsTerminal') == 'I' ) {
                        // in queue
                        $value = "NOW()";
                        break;
                    }
                }
                $stateIt->moveNext();
            }
		}

        if ( $value == 'NULL' ) {
            DAL::Instance()->Query(
                " UPDATE ".$table_name." SET StartDate = ".$value." WHERE ".$table_name."Id = ".$object_it->getId()
            );
        }
        else if ( $value != '' ) {
            DAL::Instance()->Query(
                " UPDATE ".$table_name." SET StartDate = ".$value." WHERE StartDate IS NULL AND ".$table_name."Id = ".$object_it->getId()
            );
        }
	}
}