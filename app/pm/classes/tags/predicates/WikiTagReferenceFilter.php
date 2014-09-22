<?php

class WikiTagReferenceFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM WikiPage rt WHERE rt.WikiPageId = t.Wiki AND rt.ReferenceName = '".$filter."') ";
 	}
}
