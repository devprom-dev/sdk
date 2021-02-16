<?php

class WikiNonRootEmptyFilter extends FilterPredicate
{
 	function WikiNonRootEmptyFilter() {
 		parent::__construct('root');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.Content IS NOT NULL ";
 	}
}
