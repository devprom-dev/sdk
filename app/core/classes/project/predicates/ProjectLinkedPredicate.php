<?php

class ProjectLinkedPredicate extends FilterPredicate
{
	function __construct( $type = 'common' ) {
		parent::__construct($type);
	}

 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'requests':
 				$field = 'pl.Requests';
 				break;
 				
 			case 'tasks':
 				$field = 'pl.Tasks';
 				break;
 				
 			default:
				return " AND EXISTS ( SELECT 1 FROM pm_ProjectLink pl" .
						"			   WHERE pl.Source = ".getSession()->getProjectIt()->getId()."" .
						"				 AND pl.Target = t.pm_ProjectId " .
						" 			  UNION" .
						"			  SELECT 1 FROM pm_ProjectLink pl" .
						"			   WHERE pl.Target = ".getSession()->getProjectIt()->getId()."" .
						"				 AND pl.Source = t.pm_ProjectId " .
						"			 ) ";
 		}
 		
 		return " AND EXISTS ( SELECT 1 FROM pm_ProjectLink pl" .
 			   "			   WHERE pl.Source = ".getSession()->getProjectIt()->getId()."" .
 			   "				 AND pl.Target = t.pm_ProjectId " .
 			   "				 AND ".$field." IN (1, 3)" .
 			   " 			  UNION" .
 			   "			  SELECT 1 FROM pm_ProjectLink pl" .
 			   "			   WHERE pl.Target = ".getSession()->getProjectIt()->getId()."" .
 			   "				 AND pl.Source = t.pm_ProjectId " .
 			   "				 AND ".$field." IN (2, 3) ) ".
 			   " OR t.pm_ProjectId = ".getSession()->getProjectIt()->getId();
 	}
}