<?php

class CycleStateRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	 	return $this->createIterator( array (
 	            array (
 	                    'entityId' => 1,
 	            		'ReferenceName' => 'not-passed',
 	                    'Caption' => translate('������� � �������')
 	            ),
 	            array (
 	                    'entityId' => 2,
 	            		'ReferenceName' => 'current',
 	                    'Caption' => translate('�������')
 	            ),
 	 	        array (
 	                    'entityId' => 3,
 	 	        		'ReferenceName' => 'past',
 	                    'Caption' => translate('����������')
 	            )
 	 	));
 	}
}