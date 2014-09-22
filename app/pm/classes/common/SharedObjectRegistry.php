<?php

class SharedObjectRegistry extends ObjectRegistrySQL
{
 	private $objects = array();
 	
 	function add( $class_name, $category = '' )
 	{
 	    $id = strtolower($class_name);
 	    
 		$this->objects[$id] = array( 
 			'class' => $id, 'category' => $category
 		);
 	}

 	function createSQLIterator( $sql )
 	{
 	    global $model_factory;

 	    $builders = getSession()->getBuilders('SharedObjectsBuilder'); 
 	    
 	    foreach( $builders as $builder )
 	    {
 	        $builder->build( $this );
 	    }

 		$data = array();
 		
 		foreach( $this->objects as $object )
 		{
 			$data[] = array (
 				'entityId' => $object['class'],
 				'Category' => $object['category'],
 			    'ClassName' => $object['class']
 			);
 		}

 		return $this->createIterator( $data );
 	}
}