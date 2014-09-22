<?php

class TagRequestFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM pm_RequestTag rt WHERE rt.Tag = t.TagId) ";
 	}
}
