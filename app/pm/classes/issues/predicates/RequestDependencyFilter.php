<?php

class RequestDependencyFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$type_it = getFactory()->getObject('RequestLinkType')->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('ReferenceName', preg_split('/,/',$filter))
 				)
 		);
 		
 		if ( $type_it->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND EXISTS (SELECT 1 FROM pm_ChangeRequestLink l ".
 			   "			  WHERE l.LinkType IN (".join(',',$type_it->idsToArray()).") ".
 			   "				AND t.pm_ChangeRequestId = l.SourceRequest ".  
 			   "			  UNION ".
 			   "			 SELECT 1 FROM pm_ChangeRequestLink l ".
 			   "			  WHERE l.LinkType IN (".join(',',$type_it->idsToArray()).") ".
 			   "				AND t.pm_ChangeRequestId = l.TargetRequest) ";
 	}
}
