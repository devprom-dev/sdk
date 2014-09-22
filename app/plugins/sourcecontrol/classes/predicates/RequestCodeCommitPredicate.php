<?php

class RequestCodeCommitPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$rev = $model_factory->getObject('SubversionRevision');
 		
 		$rev_it = $rev->getExact( $filter );
 		
 		if ( $rev_it->getId() < 1 ) return " AND 1 = 2 ";

        $trace = $model_factory->getObject('RequestTraceSourceCode');
 		
 		return  
 			" AND t.ClosedInVersion IS NULL ".
 		    " AND EXISTS ( SELECT 1 " .
			"                FROM pm_ChangeRequestTrace l, pm_SubversionRevision r " .
			"               WHERE l.ChangeRequest = t.pm_ChangeRequestId ".
 			"                 AND l.ObjectClass = '".$trace->getObjectClass()."'".
 			"                 AND l.ObjectId = r.pm_SubversionRevisionId ".
 			"                 AND r.RecordCreated <= ".
 			"                        (SELECT rev.RecordCreated ".
			"                           FROM pm_SubversionRevision rev ".
 			"                          WHERE rev.pm_SubversionRevisionId = ".$rev_it->getId().")".
 			"                 AND r.Repository = ".$rev_it->get('Repository')." ) ";  
 	}
}
