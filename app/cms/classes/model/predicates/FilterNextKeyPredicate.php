<?php

class FilterNextKeyPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();
 		
 		if ( $filter->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND t.".$object->getClassName()."Id > ".$filter->getId();
 	}
}
