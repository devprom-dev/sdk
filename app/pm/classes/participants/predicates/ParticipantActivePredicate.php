<?php

class ParticipantActivePredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::FilterPredicate('default');
 	}
 	
 	function _predicate( $filter )
 	{
		return " AND t.IsActive = 'Y' ";
 	}
}
