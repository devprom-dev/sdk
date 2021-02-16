<?php

class AccessRightProjectRolePredicate extends FilterPredicate
{
	function _predicate( $filter ) {
	    $values = preg_split('/,/', $filter);
 		return " AND t.ProjectRole IN 
 		            (SELECT pr.pm_ProjectRoleId FROM pm_ProjectRole pr
 		              WHERE pr.ReferenceName IN ('".(join("','", $values))."')) ";
 	}
}
