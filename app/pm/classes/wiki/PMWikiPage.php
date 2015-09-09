<?php

include_once "WikiPage.php";
include "PMWikiPageIterator.php";
include "predicates/PMWikiStageFilter.php";
include "predicates/PMWikiLinkedStateFilter.php";
include "predicates/PMWikiSourceFilter.php";
include "predicates/WikiRelatedIssuesPredicate.php";

class PMWikiPage extends WikiPage 
{
	function createIterator() 
	{
		return new PMWikiPageIterator($this);
	}
	
	function IsStatable()
	{
		global $model_factory;
		
		if ( $this->getStateClassName() == '' ) return false;
		
		$state = $model_factory->getObject($this->getStateClassName());
		
		return $state->getRecordCount() > 0;
	}
	
 	function getStateClassName()
 	{
		return '';
 	}

	//----------------------------------------------------------------------------------------------------------
	function getVersionsIt()
	{
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
		{
			$sql = 
				"SELECT tr.ObjectId WikiId, rl.Version, rl.pm_ReleaseId `Release` ".
			    " 		   FROM pm_ChangeRequestTrace tr, pm_Task ts, pm_Release rl " .
 			    "		  WHERE tr.ObjectClass = '".strtolower(get_class($this))."'" .
 			    "			AND ts.ChangeRequest = tr.ChangeRequest" .
 			    "			AND ts.Release = rl.pm_ReleaseId" .
 			    "		  UNION " .
 			    "		 SELECT tr.ObjectId WikiId, rl.Version, rl.pm_ReleaseId ".
			    " 		   FROM pm_TaskTrace tr, pm_Task ts, pm_Release rl " .
 			    "		  WHERE tr.ObjectClass = '".strtolower(get_class($this))."'" .
 			    "			AND ts.pm_TaskId = tr.Task" .
 			    "			AND ts.Release = rl.pm_ReleaseId ";
		}
		else
		{
			$sql = 
				"SELECT tr.ObjectId WikiId, req.PlannedRelease Version, NULL `Release` ".
		        " 		   FROM pm_ChangeRequestTrace tr, pm_ChangeRequest req " .
 			    "		  WHERE tr.ObjectClass = '".strtolower(get_class($this))."'" .
 			    "			AND tr.ChangeRequest = req.pm_ChangeRequestId ";
		}
		
		$sql = " SELECT t.WikiPageId, v.Version, v.Release ".
			   "   FROM ".$this->getRegistry()->getQueryClause()." t, ".
			   "        (".$sql.") v ".	
			   "  WHERE v.WikiId = t.WikiPageId ".
			   $this->getVpdPredicate().$this->getFilterPredicate().
		       "  ORDER BY t.WikiPageId ";
		
		return $this->createSQLIterator( $sql );
	}
	
	function getTypeIt()
	{
		return null;
	}
	
	function getPage()
	{
	}
	
	function getPageHistory()
	{
	}
	
	function getAttributeObject( $attr )
	{
		switch ( $attr )
		{
			case 'ParentPage':
				return $this;
				
			default:
				return parent::getAttributeObject( $attr );
		}
	}
}