<?php

class QuestionLastCommentPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			   " (SELECT IFNULL(MAX(c.RecordCreated),t.RecordCreated) FROM Comment c " .
 			   "		  WHERE c.ObjectId = t.pm_QuestionId " .
 			   "			AND c.ObjectClass = 'question' ) LastCommentDate " );

 		return $columns;
 	}
}
