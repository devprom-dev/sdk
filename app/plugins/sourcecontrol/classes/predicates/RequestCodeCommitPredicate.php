<?php

class RequestCodeCommitPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$rev_it = getFactory()->getObject('SubversionRevision')->getExact( $filter );
 		
 		if ( $rev_it->getId() < 1 ) return " AND 1 = 2 ";

        $trace = getFactory()->getObject('RequestTraceSourceCode');
 		
 		return  
 			" AND t.ClosedInVersion IS NULL ".
 		    " AND EXISTS ( SELECT 1 " .
			"                FROM pm_ChangeRequestTrace l, pm_SubversionRevision r " .
			"               WHERE l.ChangeRequest = t.pm_ChangeRequestId ".
 			"                 AND l.ObjectClass = '".$trace->getObjectClass()."'".
 			"                 AND l.ObjectId = r.pm_SubversionRevisionId ".
 			"                 AND r.RecordCreated <= '".$rev_it->get('RecordCreated')."' ".
 			"                 AND r.Repository = ".$rev_it->get('Repository')." ) ";  
 	}
}
