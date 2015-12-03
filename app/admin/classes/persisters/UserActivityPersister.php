<?php

class UserActivityPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " (SELECT DATE(MAX(u.RecordModified)) FROM pm_ProjectUse u WHERE u.Participant = t.cms_UserId) LastAuthTime ";

		$columns[] = " (SELECT DATE(u.RecordModified)
						  FROM ObjectChangeLog u
						 WHERE u.SystemUser = t.cms_UserId
						   AND u.RecordModified >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
						 ORDER BY u.RecordModified DESC LIMIT 1) LastActivityDate ";

 		return $columns;
 	}
}
