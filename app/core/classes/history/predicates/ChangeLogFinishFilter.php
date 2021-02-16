<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDateTime.php";

class ChangeLogFinishFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$mapper = new ModelDataTypeMappingDateTime();
 		return " AND t.RecordModified <= TIMESTAMP('".$mapper->map(DAL::Instance()->Escape($filter))."') ";
 	}
}
