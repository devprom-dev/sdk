<?php
include "MilestoneIterator.php";
include "predicates/MilestoneYearPredicate.php";
include "predicates/MilestoneActualPredicate.php";

class Milestone extends Metaobject
{
 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
 		parent::__construct('pm_Milestone', $registry);
 		$this->setSortDefault( new SortAttributeClause('MilestoneDate') );
	}
 	
 	function createIterator() {
 		return new MilestoneIterator( $this );
 	}
 	
	function getPage() {
		return getSession()->getApplicationUrl($this).'plan/milestone?';
	}
}