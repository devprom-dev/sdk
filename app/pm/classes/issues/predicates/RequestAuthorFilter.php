<?php

class RequestAuthorFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$users = array_filter( preg_split('/,/', $filter), function($value) {
 				return is_numeric($value) && $value > 0;
 		});
 		
 		$emails = array_filter( preg_split('/,/', $filter), function($value) {
 				return !is_numeric($value) && $value != '';
 		});
 		
 		if ( count($users) > 0 )
 		{
 			$predicate = " t.Author IN (".join(',',$users).") ".
 				" AND NOT EXISTS (SELECT 1 FROM pm_Watcher w WHERE w.Email <> '' AND w.ObjectId = t.pm_ChangeRequestId) ";
 		}
 		
 	 	if ( count($emails) > 0 )
 		{
 			$author_it = getFactory()->getObject('IssueAuthor')->getRegistry()->Query(
 					array (
 							new FilterAttributePredicate('Caption', $emails)
 					)
 			);
 			$emails = $author_it->fieldToArray('Email');
 			if ( count($emails) > 0 )
 			{
	 			$predicate .= ($predicate != '' ? " OR " : "").
	 					" EXISTS (SELECT 1 FROM pm_Watcher w WHERE w.Email IN ('".join("','", $emails)."') AND w.ObjectId = t.pm_ChangeRequestId) ";
 			}
 		}
 		
 		if ( in_array('none', $emails) )
 		{
 			$predicate .= ($predicate != '' ? " OR " : "").
 					" t.Author IS NULL AND NOT EXISTS (SELECT 1 FROM pm_Watcher w WHERE w.Email <> '' AND w.ObjectId = t.pm_ChangeRequestId) ";
 		}
 		
 		return " AND (".$predicate.") ";
 	}
}
