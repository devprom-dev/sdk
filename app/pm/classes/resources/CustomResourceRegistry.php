<?php

include_once SERVER_ROOT_PATH."core/classes/ResourceRegistry.php";

class CustomResourceRegistry extends ResourceRegistry
{
 	function createSQLIterator( $sql )
 	{
 	    if ( !$this->getObject()->getCacheEnabled() ) return ObjectRegistrySQL::createSQLIterator( $sql );
 	    
 	    $it = parent::createSQLIterator( $sql );
 	    
 	    $records = $this->getRecords();

 	    // import globally define resources into the current project
 	    
 	    $vpd_value = array_shift($this->getObject()->getVpds());

 	    foreach( $records as $key => $record ) $records[$key]['VPD'] = $vpd_value;
 	    
 	    // override original values with custom ones
 	     
 	    $it = ObjectRegistrySQL::createSQLIterator( $sql );
 	    
 	    while( !$it->end() )
 	    {
 	        if ( !array_key_exists($it->get('ResourceKey'), $records) ) 
 	        {
 	            $it->moveNext();
 	            
 	            continue;
 	        }
 	        
 	        $records[$it->get('ResourceKey')]['ResourceValue'] = $it->getHtmlDecoded('ResourceValue');
 	        
 	        $records[$it->get('ResourceKey')]['Caption'] = $it->getHtmlDecoded('ResourceValue');
 	        
 	        $records[$it->get('ResourceKey')]['OriginalId'] = $it->getId();
 	        
 	        $it->moveNext();
 	    }
 	    
 	    // sort items on ResourceValue
 		
 		uasort( $records, function($left, $right) 
 		{ 
 		    return $left['ResourceValue'] > $right['ResourceValue'] ? 1 : -1; 
 	    });

 	    return $this->createIterator( array_values($records) );
 	}	
}