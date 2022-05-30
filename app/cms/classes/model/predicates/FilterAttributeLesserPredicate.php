<?php
include_once "FilterAttributePredicate.php";

class FilterAttributeLesserPredicate extends FilterPredicate
{
    private $attribute = '';

    function __construct( $attribute, $value = 0 ) {
        $this->attribute = $attribute;
        parent::__construct($value);
    }

 	function _predicate( $filter ) {
        if ( in_array($this->getObject()->getAttributeType($this->attribute), array('integer','float')) ) {
            return " AND IFNULL(t.".$this->attribute . ",0) < " . $filter;
        }
        else {
            return " AND IFNULL(t.".$this->attribute . ",'') < '" . DAL::Instance()->Escape($filter) . "'";
        }
 	}
}