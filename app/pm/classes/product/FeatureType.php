<?php

include "FeatureTypeIterator.php";

class FeatureType extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('pm_FeatureType');
 		$this->setSortDefault( new SortOrderedClause() );
 		$this->setAttributeDescription('ChildrenLevels', text('1919'));
 	}

	function createIterator() 
	{
		return new FeatureTypeIterator( $this );
	}
	
	function getOrderStep()
	{
	    return 1;
	}
}