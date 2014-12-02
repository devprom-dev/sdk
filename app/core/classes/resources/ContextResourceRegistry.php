<?php

class ContextResourceRegistry extends ObjectRegistrySQL
{
 	private $records = array();

 	function addText( $widget_uid, $text )
 	{
		$this->records[$widget_uid] = array( 
            'cms_ResourceId' => $widget_uid,
	        'Caption' => $text 
		);
 	}
 	
 	function createSQLIterator( $sql )
 	{
 	    foreach( getSession()->getBuilders('ContextResourceBuilder') as $builder )
 	    {
     	    $builder->build( $this );
 	    }
 	 	return $this->createIterator( array_values($this->records) );
 	}
}