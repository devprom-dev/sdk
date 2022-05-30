<?php

class SearchableObjectRegistry extends ObjectRegistrySQL
{
 	var $objects = array();
 	
 	function add( $class_name, $report = '' )
 	{
 		$this->objects[] = array(
 			'class' => $class_name,
            'title' => getFactory()->getObject($class_name)->getDisplayName(),
 			'report' => $report
 		);
 	}
 	
 	function createSQLIterator( $sql )
 	{
 	    foreach( getSession()->getBuilders('SearchableObjectsBuilder') as $builder ) {
 	        $builder->build( $this );
 	    }
 	    
 	    $data = array();
 	 	foreach( $this->objects as $object )
 		{
 			$data[] = array (
 				'entityId' => $object['class'],
 				'ReferenceName' => $object['class'],
                'Caption' => $object['title'],
 				'Report' => $object['report']
 			);
 		}
 	    
 		return $this->createIterator( $data );
 	}
}
