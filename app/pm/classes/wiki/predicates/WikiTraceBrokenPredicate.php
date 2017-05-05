<?php

class WikiTraceBrokenPredicate extends FilterPredicate
{
    function __construct() {
        parent::__construct('dummy');
    }

    function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM WikiPageTrace tr WHERE tr.TargetPage = ".$this->getPK($this->getAlias())." AND tr.IsActual = 'N' AND IFNULL(tr.Baseline, 0) < 1) ";
 	}
} 
