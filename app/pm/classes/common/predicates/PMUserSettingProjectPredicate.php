<?php

class PMUserSettingProjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.Participant IN (".getSession()->getParticipantIt()->getId().", -1) ";
 	}
}
