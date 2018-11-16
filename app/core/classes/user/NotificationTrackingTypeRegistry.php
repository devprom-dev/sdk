<?php

class NotificationTrackingTypeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	  	$values = array (
            array (
                'entityId' => 'personal',
                'Caption' => text(2642)
            ),
  			array (
  			    'entityId' => 'any-changes',
	 			'Caption' => text(392)
            )
		);

 	  	if ( defined('PERMISSIONS_ENABLED') ) {
 	  	    array_unshift($values, array (
 	  	        'entityId' => 'system',
                'Caption' => text(391)
            ));
        }

		return $this->createIterator($values);
 	}
}