<?php

class WikiRootFilter extends FilterPredicate
{
 	function WikiRootFilter()
 	{
 		parent::FilterPredicate('root');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ParentPage IS NULL ";
 	}
}
