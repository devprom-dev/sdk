<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterModifiedBeforePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $mapper = new ModelDataTypeMappingDate();
 		return " AND ".$this->getAlias().".RecordModified <= '".$mapper->map(DAL::Instance()->Escape($filter))."' ";
 	}
}
