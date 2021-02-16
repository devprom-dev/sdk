<?php

class TagRequestPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Requests');
    }

    function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(DISTINCT CAST(rt.Request as CHAR)) ".
 			"	  FROM pm_RequestTag rt, pm_ChangeRequest r " .
			"	 WHERE rt.Tag = " .$this->getPK($alias)."
			       AND rt.Request = r.pm_ChangeRequestId 
			       ".(getSession()->IsRDD() ? "AND r.Type IS NOT NULL " : "")." ) Requests "
 		);
 	}
}
