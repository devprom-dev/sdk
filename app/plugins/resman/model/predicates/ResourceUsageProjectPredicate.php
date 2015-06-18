<?php

class ResourceUsageProjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        return " AND p.Project IN (".join(',',preg_split('/,/',$filter)).")";
 	}
}
