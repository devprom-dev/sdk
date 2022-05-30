<?php

class ProjectVpdPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = \TextUtils::parseIds($filter);
 		if ( count($ids) < 1 ) {
            $vpds = \TextUtils::parseItems($filter);
 		    if ( count($vpds) < 1 ) return " AND 1 = 1 ";
            return " AND ".$this->getAlias().".VPD IN ('".join("','", $vpds)."')";
        }
 		else {
            return " AND ".$this->getAlias().".VPD IN ('".join("','", getFactory()->getObject('Project')->getExact($ids)->fieldToArray('VPD'))."')";
        }
 	}
}
