<?php

class RequestFeatureFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$likes = array();
 		
 	    foreach( preg_split('/,/', $filter) as $id )
 	    {
 	    	if ( $id == 'none' ) {
 	        	$likes[] = " t.Function IS NULL ";
 	    	}
 	    	elseif( is_numeric($id) ) {
 	        	$likes[] = " EXISTS (SELECT 1 FROM pm_Function f WHERE t.Function = f.pm_FunctionId AND f.ParentPath LIKE '%,".$id.",%') ";
 	    	}
 	    }
 	    
 	    if ( count($likes) < 1 ) return " AND 1 = 2 ";

		return " AND (".join("OR", $likes).") ";
 	}
}
