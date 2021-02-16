<?php

class PMWikiTransitiveLinkedStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
            case 'empty':
                return " AND t.Content IS NULL ";

            case 'actual':
 				return " AND NOT EXISTS (SELECT 1 FROM WikiPageTrace tr ".
 					   "				  WHERE t.ParentPath LIKE CONCAT('%,',tr.TargetPage,',%') ".
 					   " 				    AND tr.IsActual = 'N') ".
                        " AND NOT EXISTS (SELECT 1 FROM pm_FunctionTrace tr ".
                        "				  WHERE t.ParentPath LIKE CONCAT('%,',tr.ObjectId,',%') ".
                        " 				    AND tr.IsActual = 'N') ";

 			case 'nonactual':
 				return " AND (EXISTS (SELECT 1 FROM WikiPageTrace tr ".
 					   "			  WHERE t.ParentPath LIKE CONCAT('%,',tr.TargetPage,',%') ".
 					   " 				AND tr.IsActual = 'N') ".
                        "   OR EXISTS (SELECT 1 FROM pm_FunctionTrace tr ".
                        "				  WHERE t.ParentPath LIKE CONCAT('%,',tr.ObjectId,',%') ".
                        " 				    AND tr.IsActual = 'N')) ";
 			default:
 				return "AND 1 = 2 ";
 		}
 	}
}
