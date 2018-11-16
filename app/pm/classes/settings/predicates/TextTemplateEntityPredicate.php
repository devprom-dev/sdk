<?php

class TextTemplateEntityPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $classes = array_merge(
            array_keys(TextTemplateEntityRegistry::getEntities()),
            array(
                'Request'
            )
        );
        if ( !in_array($filter, $classes) ) return " AND 1 = 2 ";

        if ( $filter == 'Request' ) {
            return " AND t.ObjectClass IN ('".$filter."', 'Increment') ";
        }
        else {
            return " AND t.ObjectClass = '".$filter."' ";
        }
 	}
}
