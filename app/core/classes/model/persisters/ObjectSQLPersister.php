<?php

include_once "ObjectPersister.php";

class ObjectSQLPersister extends ObjectPersister
{
 	function getPK( $alias )
 	{
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		
		return $alias.$object->getEntityRefName().'Id';
 	}
 	
 	function getSelectColumns( $alias )
 	{
 		return array();
 	}
}
