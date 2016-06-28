<?php

class ChangeLogVisibilityFilter extends FilterPredicate
{
 	function ChangeLogVisibilityFilter()
 	{
 		parent::FilterPredicate('level');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.VisibilityLevel <= 2 AND ObjectId IS NOT NULL ";
 	}
}

 