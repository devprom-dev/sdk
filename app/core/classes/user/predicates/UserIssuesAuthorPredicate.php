<?php

class UserIssuesAuthorPredicate extends FilterPredicate
{
 	function UserIssuesAuthorPredicate( $filter = 'all' )
 	{
 		parent::FilterPredicate($filter);
 	}
 	
 	function _predicate( $filter )
 	{
 		global $project_it;
 		
 		switch ( $filter )
 		{
 			case 'all':
				return " AND EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
					   "		 	   WHERE r.Author = t.cms_UserId)";

 			case 'project':
				return " AND EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
					   "		 	   WHERE r.Author = t.cms_UserId" .
					   "				 AND r.Project = ".$project_it->getId().")";
 		}
 	}
}
