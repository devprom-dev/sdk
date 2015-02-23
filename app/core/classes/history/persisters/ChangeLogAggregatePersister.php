<?php

class ChangeLogAggregatePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, " t.Caption " );
 		
 		array_push( $columns, " t.ClassName " );
 		
 		array_push( $columns, " t.EntityRefName " );
 		
 		array_push( $columns, " t.ObjectId " );

 		array_push( $columns, 
 		    " IFNULL( t.SystemUser, (SELECT p.SystemUser from pm_Participant p WHERE p.pm_ParticipantId = t.Author) ) SystemUser " );
 		
 		array_push( $columns, 
 			" MAX(t.RecordModified) RecordModified " );
 		
 		array_push( $columns, 
 			" MAX(t.RecordCreated) RecordCreated " );

        array_push( $columns, 
 			" GROUP_CONCAT(t.ChangeKind) ChangeKind " );

        array_push( $columns, 
 			" MIN(t.VisibilityLevel) VisibilityLevel " );

        array_push( $columns, 
 			" GROUP_CONCAT(DISTINCT t.Content ORDER BY t.RecordCreated DESC SEPARATOR '<br/>') Content " );
        
        array_push( $columns, 
 			" GROUP_CONCAT(t.ObjectChangeLogId) ObjectChangeLogId " );
        
 		return $columns;
 	}
}
