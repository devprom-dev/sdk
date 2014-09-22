<?php

class NotificationRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	    global $model_factory;
 	    
 	  	$values = array ( 
  			array ( 'entityId' => 'system',
	 				'Caption' => text(391) ),
  			array ( 'entityId' => 'all',
	 				'Caption' => text(392) )
		);

		$job = $model_factory->getObject('co_ScheduledJob');
		
		$job_it = $job->getByRef('ClassName', 'processdigest');

		while ( !$job_it->end() )
		{
			if ( $job_it->get('Parameters') == '' )
			{
				$job_it->moveNext();
				continue;
			}
			
			array_push( $values, 
				array( 'entityId' => $job_it->getType(),
	 				   'Caption' => $job_it->getDisplayName() ) 
	 		);

			$job_it->moveNext();
		}
 	    
		return $this->createIterator($values);
 	}
}