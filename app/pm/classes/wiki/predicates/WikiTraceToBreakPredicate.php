<?php

class WikiTraceToBreakPredicate extends FilterPredicate
{
    function __construct() {
        parent::__construct('dummy');
    }

    function _predicate( $filter )
 	{
		return " AND t.RecordModified < 
		             (SELECT MAX(c.RecordCreated) FROM WikiPageChange c 
		               WHERE c.WikiPage = t.SourcePage)
		         AND t.IsActual = 'Y'
		         AND t.Baseline IS NULL ";
 	}
} 
