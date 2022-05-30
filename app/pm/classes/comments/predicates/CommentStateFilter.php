<?php

class CommentStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
    {
        $sqls = array();
        $items = \TextUtils::parseItems($filter);
        $key = $this->getAlias() . '.' . $this->getObject()->getIdAttribute();
        $className = get_class($this->getObject());

        $userId = getSession()->getUserIt()->getId();
        if ( $userId == '' ) $userId = '0';

        $commentQuery = "Comment";

        if ( in_array('mine', $items) ) {
            $commentQuery = "(SELECT c.* FROM {$commentQuery} c  WHERE c.AuthorId = {$userId})";
        }

        if ( in_array('unanswered', $items) ) {
            $commentQuery = " (SELECT c.* FROM {$commentQuery} c 
                                    WHERE NOT EXISTS (SELECT 1 FROM Comment pc WHERE pc.PrevComment = c.CommentId))";
        }

        if ( in_array('none', $items) ) {
            $sqls[] = " NOT EXISTS (SELECT 1 FROM {$commentQuery} c 
                            WHERE c.ObjectId = {$key} AND c.ObjectClass = '{$className}') ";
        }

        if ( in_array('open', $items) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM {$commentQuery} c 
                            WHERE c.ObjectId = {$key} AND c.ObjectClass = '{$className}' AND c.Closed = 'N') ";
        }

        if ( in_array('closed', $items) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM {$commentQuery} c 
                            WHERE c.ObjectId = {$key} AND c.ObjectClass = '{$className}' AND c.Closed = 'Y') ";
        }

        if ( in_array('new', $items) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM ObjectChangeNotification c 
                                 WHERE c.ObjectId = {$key} AND c.ObjectClass = '{$className}'
                                   AND c.SystemUser = {$userId} AND c.Action = 'commented' ) ";
        }

        if ( count($sqls) < 1 ) {
            $sqls[] = "EXISTS (SELECT 1 FROM {$commentQuery} c 
                                WHERE c.ObjectId = {$key} AND c.ObjectClass = '{$className}') ";
        }

        return " AND (".join(" OR ", $sqls).")";
 	}
}
