<?php
include "FeatureIterator.php";
include "predicates/FeatureStateFilter.php";
include "predicates/FeatureRootFilter.php";
include "predicates/FeatureIssuesAllowedFilter.php";
include "predicates/FeatureStageFilter.php";
include "sorts/SortFeatureStartClause.php";
include "sorts/SortFeatureHierarchyClause.php";
include "validation/ModelValidatorChildrenLevels.php";
include "validation/ModelValidatorAvoidInfiniteLoop.php";

class Feature extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
 		parent::__construct('pm_Function', $registry, getSession()->getCacheKey());
 		$this->setSortDefault( array(
 		    new SortFeatureHierarchyClause(),
            new SortAttributeClause('Caption')
        ));
 	}

	function createIterator() {
		return new FeatureIterator( $this );
	}

	function getValidators() {
        return array(
            new ModelValidatorAvoidInfiniteLoop(),
            new ModelValidatorChildrenLevels()
        );
    }

    function getPage() {
		return getSession()->getApplicationUrl($this).'features/list?';
	}
	
	function IsDeletedCascade( $object ) {
		return false;
	}
}