<?php

class RequestIterationsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			"(SELECT GROUP_CONCAT(DISTINCT CAST(s.Release AS CHAR)) FROM pm_Task s " .
			"  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) Iterations ";

 		return $columns;
 	}
 	
 	function modify( $object_id, $parms )
 	{
 	    global $model_factory;
 	    
 	    if ( $parms['Iterations'] == '' ) return;
 	    
 	    $object_it = $this->getObject()->getExact($object_id);
 	    
 	    if ( $object_it->get('Iterations') == $parms['Iterations'] ) return; 
 	    
 	    $iteration_it = $this->getObject()->getAttributeObject('Iterations')->getExact(preg_split('/,/', $parms['Iterations']));

 	    $object_it->object->removeNotificator( 'EmailNotificator' );
 	    
 	    $this->getObject()->modify_parms( $object_it->getId(),
 	    		array(
			            'PlannedRelease' => $iteration_it->get('Version')
			    )
 	    );

		$task_it = $this->getObject()->getAttributeType('OpenTasks') != '' 
				? $object_it->getRef('OpenTasks') : getFactory()->getObject('Task')->getEmptyIterator();

		while ( !$task_it->end() )
		{
			$task_it->object->removeNotificator( 'EmailNotificator' );
			
			$task_it->object->modify_parms($task_it->getId(), array('Release' => $iteration_it->getId()));
			
			$task_it->moveNext();
		}
 	}
}
