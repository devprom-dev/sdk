<?php

class WorkItemCommentPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"( SELECT so.Caption FROM Comment so ".
 			"   WHERE so.ObjectId = ".$this->getPK($alias).
 			"     AND so.ObjectClass = LCASE(t.ObjectClass)".
 			"   ORDER BY so.RecordCreated DESC LIMIT 1 ) RecentComment ";

		$columns[] =
			"( SELECT so.AuthorId FROM Comment so ".
			"   WHERE so.ObjectId = ".$this->getPK($alias).
			"     AND so.ObjectClass = LCASE(t.ObjectClass)".
			"   ORDER BY so.RecordCreated DESC LIMIT 1 ) RecentCommentAuthor ";

 		$columns[] =
 			"( SELECT COUNT(1) FROM Comment so ".
 			"   WHERE so.ObjectId = ".$this->getPK($alias).
 			"     AND so.ObjectClass = LCASE(t.ObjectClass) ) CommentsCount ";

 		return $columns;
 	}
}
