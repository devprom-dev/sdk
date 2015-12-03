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
 			$predicate = " t.Author IN (".join(',',$users).") AND t.Customer IS NULL ";
 		}
 		
 	 	if ( count($emails) > 0 )
 		{
 			$author_it = getFactory()->getObject('IssueAuthor')->getRegistry()->Query(
 					array (
 							new FilterInPredicate(join(',',$emails))
 					)
 			);
 			$emails = $author_it->fieldToArray('Email');
 			if ( count($emails) > 0 )
 			{
	 			$predicate .= ($predicate != '' ? " OR " : "").
	 					" EXISTS (SELECT 1 FROM cms_ExternalUser u WHERE u.email IN ('".join("','", $emails)."') AND u.cms_ExternalUserId = t.Customer) ";
 			}
 		}
 		
 		if ( in_array('none', $emails) )
 		{
 			$predicate .= ($predicate != '' ? " OR " : "")." t.Author IS NULL AND t.Customer IS NULL ";
 		}
 		
 		if ( $predicate == '' ) return " AND 1 = 2 ";
 		
 		return " AND (".$predicate.") ";
 	}
}
