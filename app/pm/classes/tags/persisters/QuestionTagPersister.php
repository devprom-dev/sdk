<?php

class QuestionTagPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    global $model_factory;
 	    
 		$columns = array();
 		
 		$object = $model_factory->getObject('QuestionTag'); 
 		
 		array_push( $columns, 
 			" (SELECT GROUP_CONCAT(CAST(wt.Tag AS CHAR)) " .
			" 	 FROM pm_CustomTag wt " .
			"  	WHERE wt.ObjectId = ".$this->getPK($alias).
			"	  AND wt.ObjectClass = '".$object->getObjectClass()."' ) Tags " );
 		
 		return $columns;
 	}
} 