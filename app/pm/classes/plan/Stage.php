<?php

include "StageIterator.php";
include "StageRegistry.php";
include "predicates/StageTimelinePredicate.php";

class Stage extends Metaobject
{
 	function __construct() 
 	{
 	    parent::__construct('pm_Version', new StageRegistry($this));

        $this->addAttribute('VersionNumber', 'VARCHAR', '', false);
        $this->setAttributeGroups('VersionNumber', array('system'));
 	    $this->setSortDefault( array (
 	            new SortAttributeClause('VersionNumber')
 	    ));
 	}
 	
 	function createIterator()
 	{
 		return new StageIterator( $this );
 	}

	function IsDeletedCascade( $object )
	{
		return false;
	}
 	
	function IsUpdatedCascade( $object )
	{
		return false;
	}
		
	function getExact( $id )
 	{
 		return $this->getRegistry()->Query( array (
 				new FilterTextExactPredicate('Caption', $id)
 		));
 	}
}
