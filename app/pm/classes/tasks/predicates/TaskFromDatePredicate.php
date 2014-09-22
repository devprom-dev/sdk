<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class TaskFromDatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		if ( $date == '' ) return "";
		
		$mapper = new ModelDataTypeMappingDate();
		
		return " AND DATE(t.RecordModified) >= DATE(".DAL::Instance()->Escape($mapper->map($filter)).") ";
 	}
}
