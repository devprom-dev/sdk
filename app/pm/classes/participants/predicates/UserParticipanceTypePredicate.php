<?php

class UserParticipanceTypePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    global $model_factory;
 	    
 	    $vpds = array();
 	    
 	    $participant = $model_factory->getObject('Participant');

 	    $filter = preg_split('/,/', $filter);
 	    
 	 	if ( in_array('guest', $filter) )
 	    {
    		return " AND NOT EXISTS (SELECT 1 FROM pm_Participant r WHERE r.SystemUser = t.cms_UserId ) ";
 	    }
 	    
 	    if ( in_array('participant', $filter) )
 	    {
 	        $vpds = array_merge($vpds, array($participant->getVpdValue()));
 	    }
 	    
 	    if ( in_array('linked', $filter) )
 	    {
     	    $vpds = array_merge( $vpds, getSession()->getProjectIt()->getRef('LinkedProject')->fieldToArray('VPD') );
 	    } 
 	    
 	    if ( count($vpds) < 1 ) return " AND 1 = 2 ";
 	    
		return " AND EXISTS (SELECT 1 FROM pm_Participant r " .
			   "			  WHERE r.SystemUser = t.cms_UserId" .
			   "			    AND r.VPD IN ('".join("','", $vpds)."') ) ";
 	}
}
