<?php

class CommentAuthorPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"IFNULL((SELECT u.Caption FROM cms_User u WHERE u.cms_UserId = t.AuthorId), CONCAT_WS('', t.ExternalAuthor, ' &lt;', t.ExternalEmail, '&gt;')) AuthorName ";

 		$columns[] =  
 			"IFNULL((SELECT u.Email FROM cms_User u WHERE u.cms_UserId = t.AuthorId), t.ExternalEmail) AuthorEmail ";
 		
 		return $columns;
 	}
}
