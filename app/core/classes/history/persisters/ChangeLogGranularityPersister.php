<?php

class ChangeLogGranularityPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, " t.ChangeKind " );
 		
 		array_push( $columns, " t.ClassName " );
 		
 		array_push( $columns, " t.EntityRefName " );
 		
 		array_push( $columns, " t.ObjectId " );

        array_push( $columns, 
 			" GROUP_CONCAT(DISTINCT t.SystemUser) SystemUser " );
 		
 		array_push( $columns, 
 			" MAX(t.RecordModified) RecordModified " );
 		
 		array_push( $columns, 
 			" MAX(t.RecordCreated) RecordCreated " );

        array_push( $columns, 
 			" MIN(t.VisibilityLevel) VisibilityLevel " );

        array_push( $columns, 
 			" MAX(t.Caption) Caption " );
        
        array_push( $columns, 
 			" GROUP_CONCAT(DISTINCT t.Content ORDER BY t.RecordCreated DESC SEPARATOR '<br/>') Content " );
        
        array_push( $columns, 
 			" GROUP_CONCAT(DISTINCT t.Attributes) Attributes " );
        
        array_push( $columns, 
 			" GROUP_CONCAT(t.ObjectChangeLogId) ObjectChangeLogId " );
        
 		return $columns;
 	}
}
