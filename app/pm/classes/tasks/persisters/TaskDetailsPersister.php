<?php

class TaskDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " IFNULL(t.Caption, (SELECT r.Caption FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest)) Caption ";

		$columns[] = " t.Caption CaptionNative ";

 		$columns[] = " ( SELECT tt.Caption FROM pm_TaskType tt WHERE tt.pm_TaskTypeId = t.TaskType ) TaskTypeDisplayName ";
 		
 		$columns[] = " ( SELECT tt.ReferenceName FROM pm_TaskType tt WHERE tt.pm_TaskTypeId = t.TaskType ) TaskTypeReferenceName ";
 		
 		$columns[] = " ( SELECT IFNULL(tt.ShortCaption, SUBSTRING(tt.Caption, 1, 1)) FROM pm_TaskType tt WHERE tt.pm_TaskTypeId = t.TaskType ) TaskTypeShortName ";
 		
 		$columns[] = " ( SELECT u.cms_UserId FROM cms_User u WHERE u.cms_UserId = t.Assignee) TaskAssigneePhotoId ";
 		
 		$columns[] = " ( SELECT u.Caption FROM cms_User u WHERE u.cms_UserId = t.Assignee) TaskAssigneePhotoTitle ";

 		return $columns;
 	}
}

