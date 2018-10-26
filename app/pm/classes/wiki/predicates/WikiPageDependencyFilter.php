<?php

class WikiPageDependencyFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = TextUtils::parseIds($filter);
 		if ( count($ids) < 1 ) return " AND 1 = 2 ";

        return " AND t.Dependency LIKE CONCAT('%".get_class($this->getObject()).":".array_shift($ids)."%') ";
 	}
}
