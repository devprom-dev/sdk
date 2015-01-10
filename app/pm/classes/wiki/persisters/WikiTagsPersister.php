<?php

class WikiTagsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			"(SELECT GROUP_CONCAT(wt.Tag) FROM WikiTag wt " .
			"  WHERE wt.Wiki = ".$objectPK." ) Tags " );

 		return $columns;
 	}
}
