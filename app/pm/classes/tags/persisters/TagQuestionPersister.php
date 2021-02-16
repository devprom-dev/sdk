<?php

class TagQuestionPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Questions');
    }

 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(DISTINCT CAST(rt.ObjectId as CHAR)) ".
 			"	  FROM pm_CustomTag rt " .
			"	 WHERE rt.Tag = " .$this->getPK($alias).
			"	   AND rt.ObjectClass = 'question' ) Questions " 
 		);
 	}
}
