<?php

class ChangeLogAggregatePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array(
			" t.ClassName ",
			" t.ObjectId ",
            " t.VPD ",
            " t.Author ",
            " t.SystemUser ",
            " t.ChangeKind "
		);

        array_push( $columns,
            " MAX(t.Caption) Caption " );

 		array_push( $columns,
 			" MAX(t.RecordModified) RecordModified " );

        array_push( $columns,
            " MAX(t.UserName) UserName " );

        array_push( $columns,
            " MAX(t.EntityRefName) EntityRefName " );

        array_push( $columns,
            " GROUP_CONCAT(DISTINCT t.ObjectUrl) ObjectUrl " );

 		array_push( $columns,
 			" MAX(t.RecordCreated) RecordCreated " );

        array_push( $columns,
 			" MIN(t.VisibilityLevel) VisibilityLevel " );

        array_push( $columns, 
 			" CONCAT('<p>',GROUP_CONCAT(DISTINCT t.Content ORDER BY t.ObjectChangeLogId ASC SEPARATOR '</p><p>'),'</p>') Content " );
        
        array_push( $columns, 
 			" GROUP_CONCAT(t.ObjectChangeLogId) ObjectChangeLogId " );

        array_push( $columns,
            " GROUP_CONCAT(t.Transaction) Transaction " );

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
