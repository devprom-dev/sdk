<?php

include "MilestoneIterator.php";
include "predicates/MilestoneActualPredicate.php";
include "predicates/MilestoneYearPredicate.php";

class Milestone extends Metaobject
{
 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
 		parent::__construct('pm_Milestone', $registry);

 		$this->setSortDefault( new SortAttributeClause('MilestoneDate') );
	}
 	
 	function createIterator() 
 	{
 		return new MilestoneIterator( $this );
 	}
 	
	function getNearest( $limit = 5 ) 
	{
		return $this->getCurrent( $limit );
	}
	
	function getForCalendar()
	{
		$sql = "SELECT t.pm_MilestoneId, t.MilestoneDate, t.Caption, t.Description, p.pm_ProjectId, " .
			   "	   MONTH(t.MilestoneDate) DateMonth, QUARTER(t.MilestoneDate) DateQuarter, YEAR(t.MilestoneDate) DateYear," .
			   "	   TO_DAYS(NOW()) - TO_DAYS(t.MilestoneDate) Overdue " .
			   "  FROM pm_Milestone t, " .
			   "	   pm_Project p" .
			   " WHERE IFNULL(t.Passed, 'N') = 'N'" .
			   "   AND p.VPD = t.VPD ".$this->getVpdPredicate().$this->getFilterPredicate().
			   " ORDER BY t.MilestoneDate ASC ";
				   
		return $this->createSQLIterator( $sql );		   
	}
	
	function getPage()
	{
		return getSession()->getApplicationUrl($this).'plan/milestone?';
	}
}