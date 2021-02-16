<?php

class FilterInPredicate extends FilterPredicate
{
    function __construct($value)
    {
        if ( is_array($value) ) {
            $value = array_filter($value, function($item) {
                return $item != '';
            });
            if ( count($value) < 1 ) $value = array(0);
        }
        if ( $value == '' ) $value = array(0);
        parent::__construct($value);
    }

    function _predicate( $filter )
 	{
        $ids = \TextUtils::parseItems($filter);
 		if ( count($ids) > 0 ) {
	 		array_walk($ids, function( &$value, $key ) {
	 			$value = is_integer($value) ? intval($value) : '"'.$value.'"';
	 		});
   		    return " AND t.".$this->getObject()->getEntityRefName()."Id IN (".join(',',$ids).") ";
 		}
 		else {
 			return " AND 1 = 2 ";
 		}
 	}
}
