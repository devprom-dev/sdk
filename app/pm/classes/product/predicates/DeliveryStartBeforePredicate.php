<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class DeliveryStartBeforePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$mapper = new ModelDataTypeMappingDateTime();
 		return " AND ".$this->getAlias().".StartDate <= '".$mapper->map(DAL::Instance()->Escape($filter))."' ";
 	}
}
