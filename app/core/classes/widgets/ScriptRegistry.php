<?php

class ScriptRegistry extends ObjectRegistrySQL
{
 	var $data = array();
	
 	function createSQLIterator( $sql )
 	{
 	    $builders = getSession()->getBuilders('ScriptBuilder');

 	    foreach( $builders as $builder )
 	    {
 	        $builder->build( $this );
 	    }

 	    return $this->createIterator( $this->data );
 	}
 	
 	function addScriptFile( $file_name )
 	{
 	    $this->data[] = array (
 	    		'Caption' => addslashes(file_get_contents($file_name))
 	    );
 	}

 	function addScriptPath( $file_name )
 	{
 	    $this->data[] = array (
 	    		'ReferenceName' => $file_name
 	    );
 	}
}
