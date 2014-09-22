<?php

class UserSessionPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( $filter == '' ) $filter = '-';
 	    
 		return " AND EXISTS (SELECT 1 FROM pm_ProjectUse u " .
			   "  		  	  WHERE u.SessionHash IN ('".join('\',\'', preg_split('/,/', $filter))."')".
			   "				AND u.Participant = t.cms_UserId ) ";
 	}
}
