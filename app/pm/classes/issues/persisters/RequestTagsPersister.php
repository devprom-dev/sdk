<?php

class RequestTagsPersister extends ObjectSQLPersister
{
 	var $column_name = 'Tags';
 	
 	function getSelectColumns( $alias )
 	{
 		$columns[] =
 			"(SELECT GROUP_CONCAT(CAST(wt.Tag AS CHAR)) FROM pm_RequestTag wt " .
			"  WHERE wt.Request = ".$this->getPK($alias)." ) ".$this->column_name." ";

		$columns[] =
			"(SELECT GROUP_CONCAT(tg.Caption) FROM pm_RequestTag wt, Tag tg " .
			"  WHERE wt.Request = ".$this->getPK($alias)." AND wt.Tag = tg.TagId ) TagNames ";

 		return $columns;
 	}

 	function modify( $object_id, $parms )
 	{
 		if ( trim($parms[$this->column_name]) == '' ) return;
 		
 		$tag = getFactory()->getObject('RequestTag');
 		$tag->removeTags( $object_id );
 		
 		foreach( preg_split('/,/', $parms[$this->column_name]) as $tag_id ) {
 			$tag->bindToObject( $object_id, $tag_id );
 		}
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
