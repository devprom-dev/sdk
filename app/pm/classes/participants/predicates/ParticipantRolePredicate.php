<?php

class ParticipantRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
		$role = $model_factory->getObject('ProjectRole');
		$role_it = $role->getExact(preg_split('/,/', $filter));

		if ( $role_it->count() > 0 )
		{
			return " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r " .
				   "			  WHERE r.Participant = t.pm_ParticipantId" .
				   "				AND r.ProjectRole IN (".join($role_it->idsToArray(),',').") ) ";
		}
 	}
}
