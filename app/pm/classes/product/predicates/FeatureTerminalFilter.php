<?php

class FeatureTerminalFilter extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('-');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND NOT EXISTS (SELECT 1 FROM pm_Function t2 WHERE t2.ParentFeature = t.pm_FunctionId) ";
 	}
}
