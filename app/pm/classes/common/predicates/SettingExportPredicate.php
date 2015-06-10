<?php

class SettingExportPredicate extends FilterPredicate
{
	function __construct()
	{
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
 		return " AND t.Participant IN (".getSession()->getParticipantIt()->getId().", -1) ";
	}
}