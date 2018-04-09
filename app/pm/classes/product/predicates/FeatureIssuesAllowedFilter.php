<?php

class FeatureIssuesAllowedFilter extends FilterPredicate
{
 	function __construct() {
 		parent::__construct('-');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND (
 		            EXISTS (SELECT 1 FROM pm_FeatureType ft WHERE ft.pm_FeatureTypeId = t.Type AND ft.HasIssues = 'Y')
 		            OR NOT EXISTS (SELECT 1 FROM pm_Function c WHERE c.ParentFeature = t.pm_FunctionId)
 		         ) ";
 	}
}
