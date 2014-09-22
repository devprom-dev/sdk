<?php

class FilterVpdPredicate extends FilterPredicate
{
  	function _predicate( $filter )
 	{
 		$filter = !is_array($filter) ? preg_split('/,/', $filter) : $filter;
 		
 		$alias = $this->getAlias();
 		
 		if ( $alias != '' ) $alias .= ".";
    	
    	return " AND ".$alias."VPD IN ('".join($filter, "','")."') ";
 	}
}
