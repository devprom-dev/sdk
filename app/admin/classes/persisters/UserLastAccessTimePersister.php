<?php

class UserLastAccessTimePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " (SELECT MAX(RecordModified) FROM pm_ProjectUse u WHERE u.Participant = t.cms_UserId) LastAccessTime ";

 		return $columns;
 	}
}
