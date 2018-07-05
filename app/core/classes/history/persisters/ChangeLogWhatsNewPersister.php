<?php

class ChangeLogWhatsNewPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array(
            " t.Caption ",
			" t.ClassName ",
			" t.ObjectId ",
            " t.VPD "
		);

        array_push( $columns,
            " MIN(IF(t.ChangeKind = 'added', 'submitted', t.ChangeKind)) ChangeKind " );

 		array_push( $columns,
 			" MAX(t.RecordModified) RecordModified " );

 		array_push( $columns,
 			" MAX(t.RecordCreated) RecordCreated " );

        array_push( $columns,
 			" CONCAT('<p>',GROUP_CONCAT(DISTINCT t.Content ORDER BY t.ObjectChangeLogId DESC SEPARATOR '</p><p>'),'</p>') Content " );

        array_push( $columns,
 			" MAX(t.ObjectChangeLogId) ObjectChangeLogId " );

		return $columns;
 	}
}
