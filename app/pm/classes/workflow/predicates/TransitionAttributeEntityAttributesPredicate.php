<?php

class TransitionAttributeEntityAttributesPredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('attrs');
 	}
 	
 	function _predicate()
 	{
 		global $model_factory;
 		
 		$object = $this->getObject();
 		
 		$entity_classname = $object->getDefaultAttributeValue('Entity');
 		
 		if ( $entity_classname == '' ) return " AND 1 = 2 ";
 		
 		$entity = $model_factory->getObject($entity_classname);
 		
 		$attributes = $entity->getAttributes();
 		
 		return " AND t.ReferenceName IN ('".join("','", array_keys($attributes))."') ";
 	}
}