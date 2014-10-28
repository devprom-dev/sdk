<?php

class SettingGlobalPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.Participant = -1 ";
	}
}