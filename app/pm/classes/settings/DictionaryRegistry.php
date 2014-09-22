<?php

class DictionaryRegistry extends ObjectRegistrySQL
{
	private $entities = array();
	
 	function addEntity( & $object )
 	{
 	    if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;

 		$this->entities[] = array (
 			'entityId' => get_class($object),
 			'Caption' => $object->getDisplayName(),
 			'ReferenceName' => get_class($object)
 		);
 	}
 	
 	public function getEntities()
 	{
 		return $this->entities;
 	}
 	
 	function createSQLIterator( $sql )
 	{
 		foreach( getSession()->getBuilders('DictionaryBuilder') as $builder )
 		{
 			$builder->build( $this );
 		}
 		
 		return $this->createIterator($this->entities);  
 	}
}