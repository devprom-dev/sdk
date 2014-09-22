<?php

class WikiTraceTargetBaselinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM cms_Snapshot p " .
			   "			  WHERE p.ObjectId = t.TargetPage AND p.Caption = '".$filter."') ";
 	}
} 
