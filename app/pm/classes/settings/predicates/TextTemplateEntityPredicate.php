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

        $foundClasses = array_intersect(
            array(
                $filter, get_parent_class($filter)
            ),
            $classes
        );
        if ( count($foundClasses) < 1 ) return " AND 1 = 2 ";

        $className = array_shift($foundClasses);
        if ( $filter == 'Request' ) {
            return " AND t.ObjectClass IN ('{$className}', 'Increment') ";
        }
        else {
            return " AND t.ObjectClass = '{$className}' ";
        }
 	}
}
