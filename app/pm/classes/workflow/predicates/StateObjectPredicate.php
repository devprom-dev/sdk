<?php

class StateObjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.ObjectId = ".$filter->getId().
			   " AND t.ObjectClass = '".$filter->object->getStatableClassName()."'";
 	}
} 
