<?php
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class FilterModifiedSinceSecondsPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(".$this->getAlias().".RecordModified) <= ".intval($filter);
 	}
}
