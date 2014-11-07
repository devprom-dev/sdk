<?php

class TaskPhotoPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " ( SELECT u.cms_UserId FROM pm_Participant tp, cms_User u WHERE u.cms_UserId = tp.SystemUser AND tp.pm_ParticipantId = t.Assignee AND u.PhotoPath IS NOT NULL) OwnerPhotoId ";

 		$columns[] = " ( SELECT u.Caption FROM pm_Participant tp, cms_User u WHERE u.cms_UserId = tp.SystemUser AND tp.pm_ParticipantId = t.Assignee AND u.PhotoPath IS NOT NULL) OwnerPhotoTitle ";
 		
 		return $columns;
 	}
}
