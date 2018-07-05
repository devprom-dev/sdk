<?php

class ChangeNotificationTypeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 		return $this->createIterator(array(
            array (
                'entityId' => 'all',
                'Caption' => text(2461)
            ),
            array (
                'entityId' => 'new',
                'Caption' => text(2462)
            )
 		));  
 	}
}