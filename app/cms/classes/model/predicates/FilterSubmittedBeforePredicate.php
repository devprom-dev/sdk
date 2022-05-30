<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterSubmittedBeforePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $mapper = new ModelDataTypeMappingDate();
        $filter = array_shift(\TextUtils::parseFilterItems($filter));
 		return " AND DATE(t.RecordCreated) <= '".$mapper->map(DAL::Instance()->Escape($filter))."' ";
 	}
}
