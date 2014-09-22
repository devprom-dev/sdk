<?php

class CommentObjectFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.ObjectId IN (".join(',',$filter->idsToArray()).")".
 		       " AND LCASE(t.ObjectClass) = '".strtolower(get_class($filter->object))."'";
 	}
}
