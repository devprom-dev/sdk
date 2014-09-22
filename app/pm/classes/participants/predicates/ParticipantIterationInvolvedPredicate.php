<?php

class ParticipantIterationInvolvedPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
	    if ( !is_a($filter, 'IteratorBase') )
	    {
	        return " AND 1 = 2 ";
	    }
	    
	    return " AND EXISTS (SELECT 1 FROM pm_Task s WHERE s.Assignee = t.pm_ParticipantId AND s.Release = ".$filter->getId().") ";
 	}
}
