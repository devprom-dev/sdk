<?php

class CycleStateRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	 	return $this->createIterator( array (
 	            array (
 	                    'entityId' => 1,
 	            		'ReferenceName' => 'not-passed',
 	                    'Caption' => translate('Текущие и будущие')
 	            ),
 	            array (
 	                    'entityId' => 2,
 	            		'ReferenceName' => 'current',
 	                    'Caption' => translate('Текущие')
 	            ),
 	 	        array (
 	                    'entityId' => 3,
 	 	        		'ReferenceName' => 'past',
 	                    'Caption' => translate('Предыдущие')
 	            )
 	 	));
 	}
}