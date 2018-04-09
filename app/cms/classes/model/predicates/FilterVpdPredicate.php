<?php

class FilterVpdPredicate extends FilterPredicate
{
	function __construct( $filter = '' )
	{
		if ( $filter == '' ) $filter = 'self';
		
		parent::__construct( $filter );
	}
	
  	function _predicate( $filter )
 	{
 		if ( $filter == 'self' ) {
 			$filter = $this->getObject()->getVpds();
 		}
 		else {
 			$filter = !is_array($filter) ? preg_split('/,/', $filter) : $filter;
 		}
		if ( count($filter) < 1 ) return " AND 1 = 1 ";
 		
 		$alias = $this->getAlias();
 		if ( $alias != '' ) $alias .= ".";

    	return " AND ".$alias."VPD IN ('".join($filter, "','")."') ";
 	}
}
