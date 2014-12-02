<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterSubmittedDatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $mapper = new ModelDataTypeMappingDateTime();
 	    
 		return " AND t.RecordCreated BETWEEN '".$mapper->map(DAL::Instance()->Escape($filter." 00:00:00"))."' AND '".$mapper->map(DAL::Instance()->Escape($filter." 23:59:59"))."' ";
 	}
}
