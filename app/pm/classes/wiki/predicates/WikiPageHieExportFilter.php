<?php

class WikiPageHieExportFilter extends FilterPredicate
{
    private $options;

    function __construct($filter, $options) {
        $this->options = $options;
        parent::__construct($filter);
    }

    function _predicate( $filter )
    {
        $filter = TextUtils::parseIds($filter);
        if ( count($filter) < 1 ) return " AND 1 = 2 ";

        $sqls = array();
        $stringIds = join(',',$filter);

        if ( in_array('children', $this->options) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM WikiPage p
                                 WHERE p.WikiPageId IN ({$stringIds}) 
                                   AND FIND_IN_SET(p.WikiPageId, t.ParentPath)) ";
        }

        if ( in_array('parents', $this->options) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM WikiPage p
                                 WHERE p.WikiPageId IN ({$stringIds}) 
                                   AND FIND_IN_SET(t.WikiPageId, p.ParentPath)) ";
        }
        if ( count($sqls) < 1 ) return " AND 1 = 2 ";

 		return " AND (" . join(" OR ", $sqls) . ") ";
 	}
}
