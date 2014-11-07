<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterSubmittedDatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $mapper = new ModelDataTypeMappingDate();
 	    
 		return " AND DATE(t.RecordCreated) = DATE('".$mapper->map(DAL::Instance()->Escape($filter))."') ";
 	}
}
