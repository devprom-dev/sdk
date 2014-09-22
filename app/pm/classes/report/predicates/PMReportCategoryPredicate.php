<?php

class PMReportCategoryPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();

 		if ( !is_a($object, 'PMCustomReport') )
 		{	
 			return "AND t.Category IN ('".join("','", preg_split('/,/', $filter))."')";
 		}
 		else
 		{
 			return $filter;
 		}
 	}
}
