<?php

class RequestTestResultPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$it = getFactory()->getObject('pm_Test')->getExact( $filter );
 		
 		if ( $it->getId() < 1 )
 		{
 			return " AND 1 = 2 ";
 		}
 		
 		$trace_class = getFactory()->getObject('RequestTraceTestCaseExecution')->getObjectClass();

 		return " AND EXISTS (SELECT 1 FROM pm_TestCaseExecution e, pm_ChangeRequestTrace tr " .
 			   "			  WHERE tr.ChangeRequest = t.pm_ChangeRequestId " .
 			   "				AND tr.ObjectClass = '".$trace_class."' ".
 			   "			    AND tr.ObjectId = e.pm_TestCaseExecutionId ".
 			   "				AND e.Test = ".$it->getId().") ";
 	}
}
