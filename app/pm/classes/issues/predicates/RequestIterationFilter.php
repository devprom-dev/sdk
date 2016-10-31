<?php

class RequestIterationFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case '0':
				return " AND EXISTS (SELECT 1 FROM pm_Task ts WHERE ts.ChangeRequest = t.pm_ChangeRequestId) ";
 			
 			case 'none':
				return " AND NOT EXISTS (SELECT 1 FROM pm_Task ts WHERE ts.Release IS NOT NULL AND ts.ChangeRequest = t.pm_ChangeRequestId) ";
		    		
		    default:
		 		$iteration_it = getFactory()->getObject('Iteration')->getExact( preg_split('/,/', $filter) );
				if ( $iteration_it->count() > 0 ) {
					return " AND ( t.Iteration IN (".join($iteration_it->idsToArray(),',').") ".
                           "       OR EXISTS (SELECT 1 FROM pm_Task ts ".
						   "	  		       WHERE ts.ChangeRequest = t.pm_ChangeRequestId" .
						   "				     AND ts.Release IN (".join($iteration_it->idsToArray(),',').") ) ".
                           " )";
				}
				else {
					return " AND 1 = 2 ";
				}
 		}
 	}
}
