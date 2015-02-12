<?php

class TaskPhotoPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " ( SELECT u.cms_UserId FROM cms_User u WHERE u.cms_UserId = t.Assignee AND u.PhotoPath IS NOT NULL) OwnerPhotoId ";
 		$columns[] = " ( SELECT u.Caption FROM cms_User u WHERE u.cms_UserId = t.Assignee AND u.PhotoPath IS NOT NULL) OwnerPhotoTitle ";
 		
 		return $columns;
 	}
}
