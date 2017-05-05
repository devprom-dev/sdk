<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterDateAfterPredicate extends FilterPredicate
{
    private $attribute = '';

    function __construct( $attribute, $value = 'CURDATE()' ) {
        $this->attribute = $attribute;
        parent::__construct($value);
    }

 	function _predicate( $filter )
 	{
        if ( $filter != 'CURDATE()' ) {
            $mapper = new ModelDataTypeMappingDate();
            $filter = "'".$mapper->map(DAL::Instance()->Escape($filter))."'";
        }
 		return " AND DATE(t.".$this->attribute.") >= ".$filter." ";
 	}
}
