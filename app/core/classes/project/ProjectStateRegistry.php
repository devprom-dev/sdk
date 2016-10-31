<?php

class ProjectStateRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	 	return $this->createIterator( array (
 	            array (
 	                    'entityId' => 1,
 	            		'ReferenceName' => 'active',
 	                    'Caption' => translate('Открыты')
 	            ),
 	            array (
 	                    'entityId' => 2,
 	            		'ReferenceName' => 'closed',
 	                    'Caption' => translate('Закрыты')
 	            )
 	 	));
 	}
}