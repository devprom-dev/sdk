<?php

class QuestionTagPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns,
 			" (SELECT GROUP_CONCAT(CAST(wt.Tag AS CHAR)) " .
			" 	 FROM pm_CustomTag wt " .
			"  	WHERE wt.ObjectId = ".$this->getPK($alias).
			"	  AND wt.ObjectClass = '".getFactory()->getObject('QuestionTag')->getObjectClass()."' ) Tags " );
 		
 		return $columns;
 	}
} 