<?php

class TextTemplateEntityPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $classes = array_keys(TextTemplateEntityRegistry::getEntities());
        if ( !in_array($filter, $classes) ) return " AND 1 = 2 ";

 		return " AND t.ObjectClass = '".$filter."' ";
 	}
}
