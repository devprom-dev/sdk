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

        if ( in_array('none', $items) ) {
            $sqls[] = " NOT EXISTS (SELECT 1 FROM Comment c WHERE c.ObjectId = ".$key." AND c.ObjectClass = '".$className."') ";
        }

        if ( in_array('mine', $items) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM Comment c 
                                 WHERE c.ObjectId = ".$key." AND c.ObjectClass = '".$className."'
                                   AND c.AuthorId = ".$userId.") ";
        }

        if ( in_array('open', $items) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM Comment c WHERE c.ObjectId = ".$key." AND c.ObjectClass = '".$className."' AND c.Closed = 'N') ";
        }

        if ( in_array('closed', $items) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM Comment c WHERE c.ObjectId = ".$key." AND c.ObjectClass = '".$className."' AND c.Closed = 'Y') ";
        }

        if ( in_array('new', $items) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM ObjectChangeNotification c 
                                 WHERE c.ObjectId = ".$key." AND c.ObjectClass = '".$className."'
                                   AND c.SystemUser = ".$userId." AND c.Action = 'commented' ) ";
        }

        return " AND (" . join(" OR ", $sqls) . ") ";
 	}
}
