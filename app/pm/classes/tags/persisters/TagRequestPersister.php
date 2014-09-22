<?php

class TagRequestPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(rt.Request as CHAR)) ".
 			"	  FROM pm_RequestTag rt " .
			"	 WHERE rt.Tag = " .$this->getPK($alias)." ) Issues " 
 		);
 	}
}
