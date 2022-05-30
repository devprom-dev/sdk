<?php

class UserParticipanceTypePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $participant = getFactory()->getObject('Participant');
 	    $filter = \TextUtils::parseItems($filter);
 	    $sqls = array();
 	    
 	 	if ( in_array('guest', $filter) ) {
            $sqls[] = " NOT EXISTS (SELECT 1 FROM pm_Participant r 
                                     WHERE r.SystemUser = t.cms_UserId 
                                       AND r.VPD IN ('".join("','", $participant->getVpds())."') ) ";
 	    }

        $vpds = array();
 	    if ( in_array('participant', $filter) ) {
 	        $vpds = array_merge($vpds, $participant->getVpds());
 	    }

 	    if ( in_array('linked', $filter) ) {
     	    $vpds = array_merge( $vpds, getSession()->getLinkedIt()->fieldToArray('VPD') );
 	    } 

 	    if ( count($vpds) > 0 ) {
 	        $sqls[] = " ( t.cms_UserId = 0 
 	                      OR EXISTS (SELECT 1 FROM pm_Participant r 
 	                                  WHERE r.SystemUser = t.cms_UserId
 	                                    AND r.VPD IN ('".join("','", $vpds)."') ) ) ";
        }

		return count($sqls) > 0 ? " AND ".join( ' OR ', $sqls) : " AND 1 = 2 ";
 	}
}
