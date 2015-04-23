<?php

class CustomizableObjectRegistry extends ObjectRegistrySQL
{
 	protected $objects = array();

 	function addObject( &$object, $key = '', $title = '' )
 	{
 		$this->objects[] = array( 
 			'key' => $key == '' ? strtolower(get_class($object)) : $key,
 			'title' => $title == '' ? $object->getDisplayName() : $title
 		);
 	}
 	
 	function createSQLIterator()
 	{
 		foreach( getSession()->getBuilders('CustomizableObjectBuilder') as $builder )
 		{
 		    $builder->build($this);
 		}
 		
 		$data = array();
 		foreach( $this->objects as $object )
 		{
 			$data[] = array (
 				'entityId' => $object['key'],
 				'ReferenceName' => $object['key'],
 				'Caption' => $object['title']
 			);
 		}
 		
 		return $this->createIterator( $data );
 	}
}