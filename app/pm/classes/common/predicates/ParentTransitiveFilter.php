<?php

class ParentTransitiveFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $filter = preg_split('/,/', $filter);
 	    
 	    $filter = array_filter( $filter, function( $value ) {
 	        return is_numeric($value);
 	    });
 	    
 	    if ( count($filter) < 1 ) return " AND 1 = 2 "; 
 	    
 	    $likes = array();
 	    
 	    
 	    foreach( $filter as $id )
 	    {
 	        $likes[] = " t.ParentPath LIKE '%,".$id.",%' ";
 	    }
 	    
		return " AND (".join("OR", $likes).") ";
 	}
}
