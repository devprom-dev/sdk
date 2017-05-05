<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterDateBeforePredicate extends FilterPredicate
{
    private $attribute = '';

    function __construct( $attribute, $value ) {
        $this->attribute = $attribute;
        parent::__construct($value);
    }

 	function _predicate( $filter )
 	{
 	    $mapper = new ModelDataTypeMappingDate();
 		return " AND DATE(t.".$this->attribute.") <= '".$mapper->map(DAL::Instance()->Escape($filter))."' ";
 	}
}
