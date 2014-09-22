<?php

class ProjectVpdPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$ids = array_filter(preg_split('/,/', $filter), function( $value ) 
 		{
 		    return is_numeric($value) && $value > 0;
 		});
 		
 		if ( count($ids) < 1 ) return ' AND 1 = 2 ';
 		
 		return " AND t.VPD IN (SELECT i.VPD FROM pm_Project i ".
 			   "			    WHERE i.pm_ProjectId IN (".join(',',$ids)."))";
 	}
}
