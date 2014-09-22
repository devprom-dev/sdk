<?php

class StyleSheetRegistry extends ObjectRegistrySQL
{
 	var $data = array();
	
 	function createSQLIterator( $sql )
 	{
 	    $builders = getSession()->getBuilders('StyleSheetBuilder');

 	    foreach( $builders as $builder )
 	    {
 	        $builder->build( $this );
 	    }

 	    return $this->createIterator( $this->data );
 	}
 	
 	function addScriptFile( $file_name )
 	{
 	    $this->data[] = array (
 	    		'Caption' => file_get_contents($file_name)
 	    );
 	}
}
