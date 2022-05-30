<?php

class RequestHasTasksPredicate extends FilterPredicate
{
    function __construct() {
        parent::__construct('dummy');
    }

    function _predicate( $filter ) {
        return " AND EXISTS (SELECT 1 FROM pm_Task e WHERE e.ChangeRequest = t.pm_ChangeRequestId ) ";
 	}
}