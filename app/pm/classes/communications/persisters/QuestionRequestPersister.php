<?php

class QuestionRequestPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		global $model_factory;
 		
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		$trace = $model_factory->getObject('RequestTraceQuestion');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.ChangeRequest AS CHAR)) " .
			"     FROM pm_ChangeRequestTrace l " .
			"    WHERE l.ObjectId = " .$objectPK.
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) TraceRequests " 
 		);
 	}
}
 