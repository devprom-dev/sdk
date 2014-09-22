<?php

class ChangeLogDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			"( SELECT IFNULL( ".
 			"    (SELECT p.Caption FROM cms_User p WHERE p.cms_UserId = t.SystemUser), ".
 			"	 (SELECT IFNULL( ".
            "       (SELECT CONCAT(p.username, ' <', p.email, '>') FROM cms_ExternalUser p WHERE p.cms_ExternalUserId = t.ObjectId AND t.ClassName = 'externaluser'),".
            "       (SELECT GROUP_CONCAT(p.Email) FROM pm_Watcher p ".
 			"	     WHERE p.ObjectId = t.ObjectId ".
 			"        AND p.ObjectClass = t.ClassName) ) ) )".
 			") AuthorName " );

 		array_push( $columns, 
 			" FROM_DAYS(TO_DAYS(t.RecordModified)) ChangeDate " );

 		return $columns;
 	}
}
