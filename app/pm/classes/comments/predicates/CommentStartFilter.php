<?php

class CommentStartFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.RecordModified >= '".getSession()->getLanguage()->getDbDate($filter)."' ";
 	}
}
