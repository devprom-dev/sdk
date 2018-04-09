<?php

class CycleStateRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	 	return $this->createIterator( array (
            array (
                'entityId' => 1,
                'ReferenceName' => 'not-passed',
                'Caption' => text(2327)
            ),
            array (
                'entityId' => 3,
                'ReferenceName' => 'past',
                'Caption' => text(2503)
            ),
            array (
                'entityId' => 4,
                'ReferenceName' => 'overdue',
                'Caption' => text(2300)
            )
 	 	));
 	}
}