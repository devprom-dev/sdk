<?php

class ProjectStateRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	 	return $this->createIterator( array (
 	            array (
 	                    'entityId' => 1,
 	            		'ReferenceName' => 'N',
 	                    'Caption' => translate('Открыты')
 	            ),
 	            array (
 	                    'entityId' => 2,
 	            		'ReferenceName' => 'Y',
 	                    'Caption' => translate('Закрыты')
 	            )
 	 	));
 	}
}