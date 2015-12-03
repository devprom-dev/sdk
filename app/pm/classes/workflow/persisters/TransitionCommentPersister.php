<?php

class TransitionCommentPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
  		$objectPK = ($alias != '' ? $alias."." : "").'StateObject';
 		
 		array_push( $columns,
 			"( SELECT IFNULL(so.Comment, (SELECT co.Caption FROM Comment co WHERE co.CommentId = so.CommentObject)) FROM pm_StateObject so ".
 			"   WHERE so.pm_StateObjectId = ".$objectPK." ) TransitionComment " );
 		
 		return $columns;
 	}
}