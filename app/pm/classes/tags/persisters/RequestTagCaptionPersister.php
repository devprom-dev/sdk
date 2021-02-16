<?php

class RequestTagCaptionPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
 		array_push( $columns, 
 			"(SELECT ts.Caption FROM Tag ts WHERE ts.TagId = ".$alias."Tag ) Caption " );

 		array_push( $columns, 
 			"(SELECT COUNT(1) FROM pm_RequestTag ts WHERE ts.Tag = ".$alias."Tag ) ItemCount " );
 		
 		array_push( $columns, " ".$alias."Tag TagId " );
 		
 		return $columns;
 	}
}
