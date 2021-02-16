<?php

class DictionaryRegistry extends ObjectRegistrySQL
{
	private $entities = array();
	
 	function addEntity( $object, $caption = '' )
 	{
 		$this->entities[] = array (
 			'entityId' => get_class($object),
 			'Caption' => $caption != '' ? $caption : $object->getDisplayName(),
 			'ReferenceName' => get_class($object)
 		);
 	}
 	
 	public function getEntities()
 	{
 		return $this->entities;
 	}
 	
 	function createSQLIterator( $sql )
 	{
 		foreach( getSession()->getBuilders('DictionaryBuilder') as $builder ) {
 			$builder->build( $this );
 		}
 		
 		return $this->createIterator($this->entities);  
 	}
}