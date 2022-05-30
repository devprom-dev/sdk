<?php
include "FeatureTypeIterator.php";

class FeatureType extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_FeatureType');
 		$this->setSortDefault( new SortOrderedClause() );
 		$this->setAttributeDescription('ChildrenLevels', text('1919'));
        $this->setAttributeDescription('ReferenceName', text('2686'));
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
 	}

	function createIterator() {
		return new FeatureTypeIterator( $this );
	}
	
	function getOrderStep()	{
	    return 1;
	}

    function getPage() {
        return getSession()->getApplicationUrl($this).'project/dicts/FeatureType?';
    }
}