<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterSubmittedDatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$from_time = strftime('%Y-%m-%d %H:%M:%S', strtotime($filter));
 		$to_time = strftime('%Y-%m-%d %H:%M:%S', strtotime('-1 second', strtotime('1 day', strtotime($filter))));
 		
 	    $mapper = new ModelDataTypeMappingDateTime();
 	    
 		return " AND t.RecordCreated BETWEEN '".$mapper->map(DAL::Instance()->Escape($from_time))."' AND '".$mapper->map(DAL::Instance()->Escape($to_time))."' ";
 	}
}
