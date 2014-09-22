<?php

class CustomizableObjectRegistry extends ObjectRegistrySQL
{
 	protected $objects = array();

 	function addObject( &$object, $key = '', $title = '' )
 	{
 		array_push( $this->objects, array( 
 			'object' => $object,
 			'key' => $key == '' ? strtolower(get_class($object)) : $key,
 			'title' => $title == '' ? $object->getDisplayName() : $title
 		));
 	}
 	
 	function createSQLIterator()
 	{
 		$data = array();
 		
 		foreach( getSession()->getBuilders('CustomizableObjectBuilder') as $builder )
 		{
 		    $builder->build($this);
 		}
 		
 		foreach( $this->objects as $object )
 		{
 			$data[] = array (
 				'entityId' => $object['key'],
 				'ReferenceName' => $object['key'],
 				'Caption' => $object['title'],
 				'object' => $object['object']
 			);
 		}
 		
 		return $this->createIterator( $data );
 	}
}