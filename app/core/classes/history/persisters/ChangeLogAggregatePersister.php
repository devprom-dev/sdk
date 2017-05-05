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
			" t.ChangeKind ",
            " t.VPD "
		);
 		
 		array_push( $columns,
 		    " IFNULL( t.SystemUser, (SELECT p.SystemUser from pm_Participant p WHERE p.pm_ParticipantId = t.Author) ) SystemUser " );
 		
 		array_push( $columns, 
 			" MAX(t.RecordModified) RecordModified " );
 		
 		array_push( $columns, 
 			" MAX(t.RecordCreated) RecordCreated " );

        array_push( $columns,
 			" MIN(t.VisibilityLevel) VisibilityLevel " );

        array_push( $columns, 
 			" GROUP_CONCAT(DISTINCT t.Content ORDER BY t.RecordCreated DESC SEPARATOR '<hr/>') Content " );
        
        array_push( $columns, 
 			" GROUP_CONCAT(t.ObjectChangeLogId) ObjectChangeLogId " );

		$columns[] =
			" UNIX_TIMESTAMP(MAX(t.RecordModified)) * 100000 + (SELECT IFNULL(MAX(co_AffectedObjectsId),0) ".
			"	 FROM co_AffectedObjects o ".
			"   WHERE o.ObjectClass = '".get_class($this->getObject())."' ".
			"   ORDER BY RecordModified DESC) AffectedDate ";

		array_push( $columns,
			" FROM_DAYS(TO_DAYS(MAX(t.RecordModified))) ChangeDate " );

		return $columns;
 	}
}
