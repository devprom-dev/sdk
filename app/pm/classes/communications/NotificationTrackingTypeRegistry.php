<?php

class NotificationTrackingTypeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	  	$values = array (
  			array ( 'entityId' => 'system',
	 				'Caption' => text(391) ),
  			array ( 'entityId' => 'all',
	 				'Caption' => text(392) )
		);
		return $this->createIterator($values);
 	}
}