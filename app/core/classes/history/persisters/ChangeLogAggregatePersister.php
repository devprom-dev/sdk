<?php

class ChangeLogAggregatePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array(
			" t.Caption ",
			" t.ClassName ",
			" t.EntityRefName ",
			" t.ObjectId ",
			" t.Transaction ",
            " t.VPD ",
            " t.SystemUser ",
            " t.UserName "
		);
 		
 		array_push( $columns,
 			" MAX(t.RecordModified) RecordModified " );

        array_push( $columns,
            " MIN(t.ChangeKind) ChangeKind " );

 		array_push( $columns,
 			" MAX(t.RecordCreated) RecordCreated " );

        array_push( $columns,
 			" MIN(t.VisibilityLevel) VisibilityLevel " );

        array_push( $columns, 
 			" CONCAT('<p>',GROUP_CONCAT(DISTINCT t.Content ORDER BY t.ObjectChangeLogId DESC SEPARATOR '</p><p>'),'</p>') Content " );
        
        array_push( $columns, 
 			" MAX(t.ObjectChangeLogId) ObjectChangeLogId " );

		$columns[] =
			" UNIX_TIMESTAMP(MAX(t.RecordModified)) * 100000 + (SELECT IFNULL(MAX(co_AffectedObjectsId),0) ".
			"	 FROM co_AffectedObjects o ".
			"   WHERE o.ObjectClass = '".get_class($this->getObject())."' ".
			"   ORDER BY RecordModified DESC) AffectedDate ";

		array_push( $columns,
			"TIMESTAMP(FROM_DAYS(TO_DAYS(MAX(t.RecordModified)))) ChangeDate " );

		return $columns;
 	}
}
