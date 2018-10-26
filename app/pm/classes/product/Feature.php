<?php

include "FeatureIterator.php";
include "predicates/FeatureStateFilter.php";
include "predicates/FeatureRootFilter.php";
include "predicates/FeatureIssuesAllowedFilter.php";
include "sorts/SortFeatureStartClause.php";
include "sorts/SortFeatureHierarchyClause.php";

class Feature extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
 		parent::__construct('pm_Function', $registry, getSession()->getCacheKey());
 		$this->setSortDefault( new SortAttributeClause('Caption') );
 	}

	function createIterator() 
	{
		return new FeatureIterator( $this );
	}

	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'features/list?';
	}
	
	function IsDeletedCascade( $object )
	{
		return false;
	}
}