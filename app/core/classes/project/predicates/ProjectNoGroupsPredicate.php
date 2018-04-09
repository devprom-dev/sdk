<?php

class ProjectNoGroupsPredicate extends FilterPredicate
{
    function __construct() {
        parent::__construct('dummy');
    }

    function _predicate( $filter ) {
        return " AND NOT EXISTS (SELECT 1 FROM co_ProjectGroupLink g WHERE g.Project = t.pm_ProjectId) ";
 	}
}
