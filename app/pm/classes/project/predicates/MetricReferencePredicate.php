<?php

class MetricReferencePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !preg_match('/[a-zA-Z]+/', $filter) ) return " AND 1 = 2 ";
        return " AND ".$this->getAlias().".Metric = '".$filter."' ";
 	}
}
