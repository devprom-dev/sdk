<?php

class CommentContextPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$user_it = getSession()->getUserIt();
        $items = \TextUtils::parseItems($filter);
        $sqls = array();

        if ( in_array('mine', $items) ) {
            $sqls[] = " t.AuthorId = {$user_it->getId()} ";
        }

        if ( in_array('unanswered', $items) ) {
            $sqls[] = " NOT EXISTS (SELECT 1 FROM Comment pc WHERE pc.PrevComment = t.CommentId) ";
        }

        if ( count($sqls) < 1 ) return " AND 1 = 1 ";
        return " AND (".join(" AND ", $sqls).")";
 	}
} 
