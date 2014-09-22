<?php

class FilterAdditionalObjectsPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();
 		
 		return " OR t.".$object->getClassName()."Id IN (".join(',',preg_split('/,/',$filter)).")";
 	}
}
