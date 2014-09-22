<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterModifiedAfterPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$mapper = new ModelDataTypeMappingDate();

 		return " AND t.RecordModified >= '".$mapper->map(DAL::Instance()->Escape($filter))."' ";
 	}
}
