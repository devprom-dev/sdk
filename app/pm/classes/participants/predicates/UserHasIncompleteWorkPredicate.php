<?php

class UserHasIncompleteWorkPredicate extends FilterPredicate
{
 	function __construct() {
 		parent::__construct('default');
 	}

 	function _predicate( $filter )
 	{
		return " AND (
		        EXISTS (SELECT 1 FROM pm_Task s WHERE s.Assignee = t.cms_UserId)
		        OR EXISTS (SELECT 1 FROM pm_ChangeRequest s WHERE s.Owner = t.cms_UserId)
		    )";
 	}
}
