<?php

class TaskPhotoPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " ( SELECT u.cms_UserId FROM cms_User u WHERE u.cms_UserId = t.Assignee) OwnerPhotoId ";
 		$columns[] = " ( SELECT u.Caption FROM cms_User u WHERE u.cms_UserId = t.Assignee) OwnerPhotoTitle ";
 		
 		return $columns;
 	}
}
