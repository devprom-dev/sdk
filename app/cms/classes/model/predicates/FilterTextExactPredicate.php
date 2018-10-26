<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingString.php";

class FilterTextExactPredicate extends FilterAttributePredicate
{
 	function _predicate( $filter )
 	{
		$mapper = new ModelDataTypeMappingString();
		return " AND ".$this->getAlias().".".$this->getAttribute()." = '".htmlspecialchars(trim($mapper->map(DAL::Instance()->Escape($filter))), ENT_QUOTES | ENT_HTML401, APP_ENCODING)."' ";
 	}
}
