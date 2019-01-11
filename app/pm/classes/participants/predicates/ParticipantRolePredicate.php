<?php

class ParticipantRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $role_it = getFactory()->getObject('ProjectRole')->getExact(preg_split('/,/', $filter));
        if ( $role_it->count() < 1 ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r " .
               "			  WHERE r.Participant = t.pm_ParticipantId" .
               "				AND r.ProjectRole IN (".join($role_it->idsToArray(),',').") ) ";
 	}
}
