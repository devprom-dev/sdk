<?php

class ModuleCategoryRegistry extends ObjectRegistrySQL
{
 	var $data = array();
	
 	function createSQLIterator( $sql )
 	{
 	    $builders = getSession()->getBuilders('ModuleCategoryBuilder');

 	    foreach( $builders as $builder )
 	    {
 	        $builder->build( $this );
 	    }
 	    
 	    return $this->createIterator( $this->data );
 	}
 	
 	function add( $ref_name, $title )
 	{
 	    $this->data[] = array (
 	    		'entityId' => $ref_name,
 	    		'ReferenceName' => $ref_name,
 	    		'Caption' => $title,
 	    		'VPD' => $this->getObject()->getVpdValue()
 	    );
 	}
}
