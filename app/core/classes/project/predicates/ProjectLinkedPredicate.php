<?php

class ProjectLinkedPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $project_it;
 		
 		switch ( $filter )
 		{
 			case 'requests':
 				$field = 'pl.Requests';
 				break;
 				
 			case 'tasks':
 				$field = 'pl.Tasks';
 				break;
 				
 			default:
 				return "";
 		}
 		
 		return " AND EXISTS ( SELECT 1 FROM pm_ProjectLink pl" .
 			   "			   WHERE pl.Source = ".$project_it->getId()."" .
 			   "				 AND pl.Target = t.pm_ProjectId " .
 			   "				 AND ".$field." IN (1, 3)" .
 			   " 			  UNION" .
 			   "			  SELECT 1 FROM pm_ProjectLink pl" .
 			   "			   WHERE pl.Target = ".$project_it->getId()."" .
 			   "				 AND pl.Source = t.pm_ProjectId " .
 			   "				 AND ".$field." IN (2, 3) ) ".
 			   " OR t.pm_ProjectId = ".$project_it->getId();
 	}
}