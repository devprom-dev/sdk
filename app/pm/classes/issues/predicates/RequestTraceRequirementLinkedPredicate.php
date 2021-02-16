<?php

class RequestTraceRequirementLinkedPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        return " AND EXISTS (SELECT 1 FROM WikiPage p 
                              WHERE p.ParentPath LIKE '%,".$filter->getId().",%' 
                                AND p.WikiPageId = t.ObjectId)
                 AND EXISTS (SELECT 1 FROM pm_ChangeRequest r 
                              WHERE r.pm_ChangeRequestId = t.ChangeRequest
                                AND r.Description LIKE '%{{%')
                 AND t.Type = '".REQUEST_TRACE_PRODUCT."' ";
 	}
} 
