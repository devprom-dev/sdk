<?php

class TagFeaturePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(rt.ObjectId as CHAR)) ".
 			"	  FROM pm_CustomTag rt " .
			"	 WHERE rt.Tag = " .$this->getPK($alias).
			"	   AND rt.ObjectClass = 'feature' ) Features " 
 		);
 	}
}
