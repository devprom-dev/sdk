<?php

class ResourcePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( strtolower(get_class($filter->object)) )
 		{
 			case 'user':
 				return " AND p.SystemUser IN (".join(',', $filter->idsToArray()).")";

 			case 'projectrolebase':
 				return " AND prr.ProjectRoleBase IN (".join(',', $filter->idsToArray()).") ";
 		}
 	}
}
