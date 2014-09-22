<?php

class SearchableObjectRegistry extends ObjectRegistrySQL
{
 	var $objects = array();
 	
 	function add( $class_name, $attributes = array(), $report = '' )
 	{
 		$this->objects[] = array( 
 			'class' => $class_name,
 			'attributes' => $attributes,
 			'report' => $report
 		);
 	}
 	
 	function createSQLIterator( $sql )
 	{
 	    foreach( getSession()->getBuilders('SearchableObjectsBuilder') as $builder )
 	    {
 	        $builder->build( $this );
 	    }
 	    
 	    $data = array();
 	    
 	 	foreach( $this->objects as $object )
 		{
 			$data[] = array (
 				'entityId' => $object['class'],
 				'ReferenceName' => $object['class'],
 				'attributes' => $object['attributes'],
 				'Report' => $object['report']
 			);
 		}
 	    
 		return $this->createIterator( $data );
 	}
}