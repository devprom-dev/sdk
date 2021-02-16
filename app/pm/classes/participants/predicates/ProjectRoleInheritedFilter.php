<?php

class ProjectRoleInheritedFilter extends FilterPredicate
{
 	function ProjectRoleInheritedFilter() {
 		parent::__construct('inherited');
 	}
 	
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'inherited':
 				return " AND EXISTS (SELECT 1 FROM pm_ProjectRole r " .
 					   " 			  WHERE r.pm_ProjectRoleId = t.ProjectRoleBase)";
 		}
 	}
}
