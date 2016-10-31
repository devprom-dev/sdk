<?php

class TransitionAttributeEntityAttributesPredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('attrs');
 	}
 	
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();
 		
 		$entity_classname = $object->getDefaultAttributeValue('Entity');
 		if ( $entity_classname == '' ) return " AND 1 = 2 ";
 		
 		return " AND t.ReferenceName IN ('".join("','", array_keys(getFactory()->getObject($entity_classname)->getAttributes()))."') ";
 	}
}