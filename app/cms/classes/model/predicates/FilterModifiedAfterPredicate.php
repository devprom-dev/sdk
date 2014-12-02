<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterModifiedAfterPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$dt = new DateTime($filter);
 		$mapper = new ModelDataTypeMappingDateTime();
 		
 		return " AND t.RecordModified >= '".$mapper->map(DAL::Instance()->Escape($dt->format("Y-m-d H:i:s")))."' ";
 	}
}
