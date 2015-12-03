<?php

class CustomizableObjectRegistry extends ObjectRegistrySQL
{
 	protected $objects = array();

 	function add( $class_name, $key = '', $title = '' )
 	{
 		$this->objects[] = array( 
 			'key' => $key == '' ? strtolower($class_name) : $key,
			'title' => $title
 		);
 	}
 	
 	function createSQLIterator()
 	{
 		foreach( getSession()->getBuilders('CustomizableObjectBuilder') as $builder ) {
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

	public function getData() {
		return $this->object;
	}
}