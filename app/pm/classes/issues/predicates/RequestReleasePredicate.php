<?php

class RequestReleasePredicate extends FilterAttributePredicate
{
 	function _predicate( $filter )
 	{
        if ( strpos($filter, 'notpassed') !== false ) {
            $filter = str_replace('notpassed',
                join(',',getFactory()->getObject('ReleaseActual')->getAll()->idsToArray()));
        }
        return parent::_predicate($filter);
 	}
} 
