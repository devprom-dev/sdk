<?php

class UserParticipanceProjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = preg_split('/,/', $filter);
 		
 		if ( in_array('none', $ids) )
 		{
			return " AND NOT EXISTS (SELECT 1 FROM pm_Participant n " .
				   "			  	  WHERE n.SystemUser = t.cms_UserId ) ";
 		}
 		
 		$ids = array_filter( $ids, function( $value ) {
 			return $value > 0;
 		});
 		
 		if ( count($ids) < 1 ) return " AND 1 = 2 ";
 		
		$project_it = getFactory()->getObject('Project')->getExact($ids);

		if ( $project_it->count() < 1 ) return " AND 1 = 2 ";
		 
		return " AND EXISTS (SELECT 1 FROM pm_Participant n " .
			   "			  WHERE n.SystemUser = t.cms_UserId ".
			   "                AND n.Project IN (".join($project_it->idsToArray(),',').") ) ";
 	}
}
