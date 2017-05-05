<?php

class UserWorkerPredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('default');
 	}
 	
 	function _predicate( $filter )
 	{
 		if ( !defined('PERMISSIONS_ENABLED') ) {
 			return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList bl WHERE bl.SystemUser = t.cms_UserId) ";
 		}
 			
 		$ids = array_filter(
	 				array_merge(
			 				array(getSession()->getProjectIt()->getId()),
			 				preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject'))
	 				),
 					function($value) {
 						return $value > 0;
 					}
 			);
 		
 		if ( count($ids) < 1 ) $ids = array(0);
 		
		return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList bl WHERE bl.SystemUser = t.cms_UserId) ".
			   " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_Participant pt " .
			   "			  WHERE r.Participant = pt.pm_ParticipantId" .
			   "			    AND pt.IsActive = 'Y' ".
			   "			    AND pt.SystemUser = t.cms_UserId ".
			   "				AND pt.Project IN (".join(',',$ids).") ) ";
 	}
}
