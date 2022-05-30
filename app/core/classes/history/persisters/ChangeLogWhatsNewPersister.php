<?php

class ChangeLogWhatsNewPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array(
			" t.ClassName ",
    		" t.ObjectId ",
            " t.VPD "
		);

        array_push( $columns,
            " MAX(t.Caption) Caption " );

        array_push( $columns,
            " MIN(IF(t.ChangeKind = 'added', 'submitted', t.ChangeKind)) ChangeKind " );

 		array_push( $columns,
 			" MAX(t.RecordModified) RecordModified " );

 		array_push( $columns,
 			" MAX(t.RecordCreated) RecordCreated " );

        array_push( $columns,
            " MAX(t.SystemUser) SystemUser " );

        array_push( $columns,
 			" CONCAT('<p>',GROUP_CONCAT(DISTINCT CONCAT(IFNULL((SELECT p.Caption FROM cms_User p WHERE p.cms_UserId = t.SystemUser), t.Author), ': ', t.Content) ORDER BY t.ObjectChangeLogId DESC SEPARATOR '</p><p>'),'</p>') Content " );

        array_push( $columns,
 			" MAX(t.ObjectChangeLogId) ObjectChangeLogId " );

        array_push( $columns,
            "(SELECT MAX(r.pm_ProjectId) FROM pm_Project r WHERE r.VPD = t.VPD) Project " );

		return $columns;
 	}
}
