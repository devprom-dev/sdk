<?php

class FeatureRootFilter extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('-');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ParentFeature IS NULL ";
 	}
}
