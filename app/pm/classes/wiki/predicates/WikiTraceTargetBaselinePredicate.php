<?php

class WikiTraceTargetBaselinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM cms_Snapshot p, WikiPage d " .
			   "			  WHERE p.ObjectId = d.DocumentId ".
			   "			    AND d.WikiPageId = t.TargetPage ".
			   "				AND p.Caption = '" . DAL::Instance()->Escape($filter) . "') ";
 	}
} 
