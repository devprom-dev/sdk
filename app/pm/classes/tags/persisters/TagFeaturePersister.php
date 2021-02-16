<?php

class TagFeaturePersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Features');
    }

 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(DISTINCT CAST(rt.ObjectId as CHAR)) ".
 			"	  FROM pm_CustomTag rt " .
			"	 WHERE rt.Tag = " .$this->getPK($alias).
			"	   AND rt.ObjectClass = 'feature' ) Features " 
 		);
 	}
}
