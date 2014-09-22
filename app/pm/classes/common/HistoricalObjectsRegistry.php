<?php

class HistoricalObjectsRegistry extends ObjectRegistrySQL
{
 	private $data = array();
 	
 	function add( $class_name, $attributes )
 	{
 		$this->data[] = array( 
 			'class' => $class_name,
 			'attributes' => $attributes
 		);
 	}

 	function createSQLIterator( $sql )
 	{
 	    $builders = getSession()->getBuilders('HistoricalObjectsRegistryBuilder'); 
 	    
 	    foreach( $builders as $builder )
 	    {
 	        $builder->build( $this );
 	    }

 		$data = array();
 		
 		foreach( $this->data as $object )
 		{
 			$data[] = array (
 				'entityId' => $object['class'],
 				'ReferenceName' => $object['class'],
 				'attributes' => $object['attributes']
 			);
 		}

 		return $this->createIterator( $data );
 	}
}