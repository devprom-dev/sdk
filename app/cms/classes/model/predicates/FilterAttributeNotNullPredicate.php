<?php
include_once "FilterAttributePredicate.php";

class FilterAttributeNotNullPredicate extends FilterPredicate
{
    private $attribute = '';

    function __construct( $attribute ) {
        $this->attribute = $attribute;
        parent::__construct('dummy');
    }

 	function _predicate( $filter ) {
		return " AND t.".$this->attribute." IS NOT NULL ";
 	}
}