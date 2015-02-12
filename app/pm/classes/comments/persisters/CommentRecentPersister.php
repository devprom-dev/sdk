<?php

class CommentRecentPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"( SELECT so.Caption FROM Comment so ".
 			"   WHERE so.ObjectId = ".$this->getPK($alias).
 			"     AND LCASE(so.ObjectClass) = '".strtolower(get_class($this->getObject()))."'".
 			"   ORDER BY so.RecordCreated DESC LIMIT 1 ) RecentComment ";
 		
 		$columns[] =  
 			"( SELECT COUNT(1) FROM Comment so ".
 			"   WHERE so.ObjectId = ".$this->getPK($alias).
 			"     AND LCASE(so.ObjectClass) = '".strtolower(get_class($this->getObject()))."' ) CommentsCount ";

 		return $columns;
 	}
}
