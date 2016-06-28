<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDateTime.php";

class ChangeLogStartServerFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.RecordModified >= TIMESTAMP('".DAL::Instance()->Escape($filter)."')";
 	}
}
