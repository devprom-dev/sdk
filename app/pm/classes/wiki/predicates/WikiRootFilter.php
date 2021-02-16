<?php

class WikiRootFilter extends FilterPredicate
{
 	function WikiRootFilter() {
 		parent::__construct('root');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ParentPage IS NULL ";
 	}
}
