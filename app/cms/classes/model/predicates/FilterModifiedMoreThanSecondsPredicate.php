<?php

class FilterModifiedMoreThanSecondsPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $now = \SystemDateTime::date();
 		return " AND UNIX_TIMESTAMP('{$now}') - UNIX_TIMESTAMP(".$this->getAlias().".RecordModified) > ".intval($filter);
 	}
}
