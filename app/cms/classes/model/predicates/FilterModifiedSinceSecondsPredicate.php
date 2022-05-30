<?php

class FilterModifiedSinceSecondsPredicate extends FilterPredicate
{
 	function _predicate( $filter ) {
 		return " AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(".$this->getAlias().".RecordModified) <= ".intval($filter);
 	}
}
