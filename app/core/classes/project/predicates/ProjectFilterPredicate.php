<?php

class ProjectFilterPredicate extends FilterPredicate
{
    function __construct()
    {
        parent::__construct('project');
    }
    
    function _predicate( $filter )
    {
        return " AND IFNULL(t.IsTender,'N') = 'N' ";
    }
}