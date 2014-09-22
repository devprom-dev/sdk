<?php

class WikiNonRootEmptyFilter extends FilterPredicate
{
 	function WikiNonRootEmptyFilter()
 	{
 		parent::FilterPredicate('root');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.Content IS NOT NULL ";
 	}
}
