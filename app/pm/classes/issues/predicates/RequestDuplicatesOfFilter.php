<?php

class RequestDuplicatesOfFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$type_it = getFactory()->getObject('RequestLinkType')->getByRef('ReferenceName', 'duplicates');
 		
 		if ( $type_it->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND EXISTS (SELECT 1 FROM pm_ChangeRequestLink l ".
 			   "			  WHERE l.LinkType = ".$type_it->getId().
 			   "				AND t.pm_ChangeRequestId = l.SourceRequest ".  
 			   "				AND l.TargetRequest = ".$filter.
 			   "			  UNION ".
 			   "			 SELECT 1 FROM pm_ChangeRequestLink l ".
 			   "			  WHERE l.LinkType = ".$type_it->getId().
 			   "				AND t.pm_ChangeRequestId = l.TargetRequest ".  
 			   "				AND l.SourceRequest = ".$filter.") ";
 	}
}
