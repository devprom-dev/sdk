<?php

class FilterInstallationUIDPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND ICQ LIKE '%".$filter."%'";
 	}
}
