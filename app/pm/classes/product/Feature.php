<?php
include "FeatureIterator.php";
include "predicates/FeatureStateFilter.php";
include "predicates/FeatureIssuesAllowedFilter.php";
include "predicates/FeatureStageFilter.php";
include "sorts/SortFeatureStartClause.php";
include "validation/ModelValidatorChildrenLevels.php";

class Feature extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
 		parent::__construct('pm_Function', $registry, getSession()->getCacheKey());
 		$this->setSortDefault( array(
 		    new SortObjectHierarchyClause(),
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