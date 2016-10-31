<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingString.php";

class FilterTextExactPredicate extends FilterAttributePredicate
{
 	function _predicate( $filter )
 	{
		$mapper = new ModelDataTypeMappingString();
		return " AND ".$this->getAlias().".".$this->getAttribute()." = ".$this->getObject()->formatValueForDB($this->getAttribute(), $mapper->map(DAL::Instance()->Escape($filter)));
 	}
}
