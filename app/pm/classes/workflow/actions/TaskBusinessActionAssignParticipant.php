<?php
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionAssignParticipant extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '223125138';
 	}
	
	function apply( $object_it )
 	{
        $data = $this->getData();
        $userId = getSession()->getUserIt()->getId();

        if ( $data['Assignee'] > 0 || $data['Assignee'] == $userId ) return true;

 	    $object_it->object->modify_parms($object_it->getId(),
            array(
                'Assignee' => $userId
            )
    	);
 		return true;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(1376);
 	}
}
