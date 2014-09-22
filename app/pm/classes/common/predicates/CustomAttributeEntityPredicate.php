<?php

class CustomAttributeEntityPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$entities = array();
 		 		
 		$sql = array();
 		
 		foreach( preg_split('/,/', $filter) as $item )
 		{ 
 		    $parts = preg_split('/\:/', $item);
 		    
 		    if ( count($parts) > 1 )
 		    {
 		        $class_name = $model_factory->getClass($parts[0]);
 		        
 		        if ( !class_exists($class_name, false) ) continue;
 		        
    	 		$object = $model_factory->getObject($class_name);
    	 		
    	 		if ( !is_subclass_of( $object, 'Metaobject') ) continue;
	 		
    	 		$sql[] = "t.EntityReferenceName = '".$class_name."' AND t.ObjectKind = '".$parts[1]."'";
 		    }
 		    else
 		    {
 		        $class_name = $model_factory->getClass($item);
 		        
 		        if ( !class_exists($class_name, false) ) continue;
 		        
	 		    $object = $model_factory->getObject($class_name);
	 		
	 		    if ( !is_subclass_of( $object, 'Metaobject') ) continue;
 		        
	 		    $entities[] = $class_name;  
 		    }
 		}
 		
 		if ( count($entities) > 0 )
 		{
 		    $sql[] = " t.EntityReferenceName IN ('".join("','", $entities)."') "; 
 		}
 		
 		return count($sql) > 0 ? " AND (".join(" OR ", $sql).") " : " AND 1 = 2 ";
 	}
}