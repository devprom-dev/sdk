<?php

class CommentFinishFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.RecordModified <= '".getSession()->getLanguage()->getDbDate($filter)."' ";
 	}
}
