<?php

class SubversionRevisionRequirementPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$requirement = $model_factory->getObject('Requirement');
 		$requirement_it = $requirement->getExact($filter);
 		
 		if ( $requirement_it->count() < 1 )
 		{
 			return "1 = 2"; 
 		}
 		
		return " AND EXISTS ( SELECT 1 " .
			   "			    FROM pm_ChangeRequestTrace s, pm_ChangeRequestTrace r " .
			   "			   WHERE s.ObjectId = t.pm_SubversionRevisionId" .
			   "				 AND s.ObjectClass = 'SubversionRevision'" .
			   "				 AND s.ChangeRequest = r.ChangeRequest" .
			   "			     AND r.ObjectId = ".$requirement_it->getId()." " .
			   "				 AND r.ObjectClass = 'Requirement' ) ";
 	}
}
