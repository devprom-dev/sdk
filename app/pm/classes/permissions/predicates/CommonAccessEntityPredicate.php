<?php

class CommonAccessEntityPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$entity_it = getEntity()->getObject('entity')->getExact( preg_split('/,/', $filter) );
 		
 		if ( $entity_it->get('ReferenceName') == '' ) return " AND 1 = 2 ";
 		
 		$object = getFactory()->getObject($entity_it->get('ReferenceName'));
 		
		return " AND t.ReferenceName LIKE '".get_class($object).".%' ";
 	}
}
