<?php

class CommentObjectStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'resolved':
 				return " AND (SELECT so.State FROM pm_StateObject so ".
 					   "	   WHERE so.pm_StateObjectId = t.StateObject) = ".
 					   "	 (SELECT s.pm_StateId FROM pm_State s ".
 					   "	   WHERE LCASE(s.ObjectClass) = LCASE(t.ObjectClass) ".
 					   "		 AND s.IsTerminal = 'Y' ".
 					   "		 AND s.VPD = t.VPD LIMIT 1) ";
 				
 			case 'notresolved':
 				return " AND ((SELECT so.State FROM pm_StateObject so ".
 					   "	   WHERE so.pm_StateObjectId = t.StateObject) IN ".
 					   "	 (SELECT s.pm_StateId FROM pm_State s ".
 					   "	   WHERE LCASE(s.ObjectClass) = LCASE(t.ObjectClass) ".
 					   "		 AND s.IsTerminal = 'N' ".
 					   "		 AND s.VPD = t.VPD) ".
 					   "  OR NOT EXISTS (SELECT 1 FROM pm_StateObject so ".
 					   "	   			  WHERE so.pm_StateObjectId = t.StateObject)) ";
 				
 			default:
 				return "";
 		}
 	}
} 
