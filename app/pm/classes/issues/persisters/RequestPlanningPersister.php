<?php

class RequestPlanningPersister extends ObjectSQLPersister
{
 	function modify( $object_id, $parms )
 	{
 		global $model_factory;
 		
 		if ( $parms['Release'] > 0 )
 		{
 			$iteration = $model_factory->getObject('Iteration');
 			$iteration_it = $iteration->getExact( $parms['Release'] );

 			if ( $iteration_it->getId() > 0 )
 			{
	 			$object = $this->getObject();
	 			$object->modify_parms( $object_id, array (
	 				'PlannedRelease' => $iteration_it->get('Version')
	 			));
 			} 
 		}
 	}
}
