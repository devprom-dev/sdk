<?php

class NotificationRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	  	$values = array (
 	  	    array(
 	  	        'entityId' => '',
                'Caption' => text(2451),
            ),
  			array (
  			    'entityId' => 'direct',
	 			'Caption' => text(2466)
            )
		);

		$job_it = getFactory()->getObject('co_ScheduledJob')->getByRef('ClassName', 'processdigest');
		while ( !$job_it->end() )
		{
			if ( $job_it->get('Parameters') == '' ) {
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