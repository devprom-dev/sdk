<?php

class WikiNonRootFilter extends FilterPredicate
{
 	function WikiNonRootFilter()
 	{
 		parent::FilterPredicate('root');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ParentPage IS NOT NULL ";
 	}
}
 