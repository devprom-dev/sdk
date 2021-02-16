<?php

class CommentRecentPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"( SELECT so.Caption FROM Comment so ".
 			"   WHERE so.ObjectId = ".$this->getPK($alias).
 			"     AND so.ObjectClass = '".get_class($this->getObject())."'".
 			"   ORDER BY so.RecordCreated DESC LIMIT 1 ) RecentComment ";

        $columns[] =
            "( SELECT so.RecordModified FROM Comment so ".
            "   WHERE so.ObjectId = ".$this->getPK($alias).
            "     AND so.ObjectClass = '".get_class($this->getObject())."'".
            "   ORDER BY so.RecordCreated DESC LIMIT 1 ) RecentCommentDate ";

		$columns[] =
			"( SELECT so.AuthorId FROM Comment so ".
			"   WHERE so.ObjectId = ".$this->getPK($alias).
			"     AND so.ObjectClass = '".get_class($this->getObject())."'".
			"   ORDER BY so.RecordCreated DESC LIMIT 1 ) RecentCommentAuthor ";

 		$columns[] =
 			"( SELECT COUNT(1) FROM Comment so ".
 			"   WHERE so.ObjectId = ".$this->getPK($alias).
 			"     AND so.ObjectClass = '".get_class($this->getObject())."' AND so.Closed = 'N') CommentsCount ";

 		$userIt = getSession()->getUserIt();
 		if ( $userIt->getId() != '' ) {
            $columns[] =
                "( SELECT IFNULL(MAX(so.RecordCreated),'') FROM ObjectChangeNotification so ".
                "   WHERE so.ObjectId = ".$this->getPK($alias).
                "     AND so.ObjectClass = '".get_class($this->getObject())."' ".
                "     AND so.SystemUser = ".$userIt->getId().
                "     AND so.Action = 'commented' LIMIT 1 ) NewComments ";
        }

 		return $columns;
 	}
}
