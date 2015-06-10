<?php

class ResourceRegistry extends ObjectRegistrySQL
{
 	private $records = array();

 	function addText( $key, $value, $original = '' )
 	{
		$this->records[$key] = array( 
            'cms_ResourceId' => $key,
	        'Caption' => $value == '' ? $key : $value,
	        'ResourceKey' => $key, 
		    'ResourceValue' => $value == '' ? $key : $value,
		    'OriginalValue' => $original == '' ? ($value == '' ? $key : $value) : $original 
		);
 	}
 	
 	function createSQLIterator( $sql )
 	{
 	    foreach( getSession()->getBuilders('ResourceBuilder') as $builder ) {
     	    $builder->build( $this );
 	    }
 	 	return $this->createIterator( array_values($this->getRecords()) );
 	}
 	
 	function & getRecords()
 	{
 	    return $this->records;
 	}
}