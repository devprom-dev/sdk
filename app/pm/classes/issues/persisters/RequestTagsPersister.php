<?php

class RequestTagsPersister extends ObjectSQLPersister
{
 	var $column_name = 'Tags';
 	
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			"(SELECT GROUP_CONCAT(CAST(wt.Tag AS CHAR)) FROM pm_RequestTag wt " .
			"  WHERE wt.Request = ".$objectPK." ) ".$this->column_name." " );

 		return $columns;
 	}

 	function modify( $object_id, $parms )
 	{
 		global $model_factory;
 		
 		if ( trim($parms[$this->column_name]) == '' ) return;
 		
 		$tag = $model_factory->getObject('RequestTag');
 		
 		$tag->removeTags( $object_id );
 		
 		foreach( preg_split('/,/', $parms[$this->column_name]) as $tag_id )
 		{
 			$tag->bindToObject( $object_id, $tag_id );
 		}
 	}
}
