<?php
include "RequestIterator.php";
include_once "persisters/IssueLinkedIssuesPersister.php";
include_once "persisters/RequestIterationsPersister.php";
include_once "predicates/RequestIterationFilter.php";
include_once "predicates/RequestAuthorFilter.php";
include_once "predicates/RequestEstimationFilter.php";
include_once "predicates/RequestTagFilter.php";
include_once "predicates/RequestTestResultPredicate.php";
include_once "predicates/RequestStagePredicate.php";
include_once "predicates/RequestTaskTypePredicate.php";
include_once "predicates/RequestTaskStatePredicate.php";
include_once "predicates/RequestNonPlannedPredicate.php";
include_once "predicates/RequestReleasePredicate.php";
include_once "predicates/RequestDuplicatesOfFilter.php";
include_once "predicates/RequestImplementationFilter.php";
include_once "predicates/RequestDependencyFilter.php";
include_once "predicates/RequestFeatureFilter.php";
include_once "predicates/RequestFinishAfterPredicate.php";
include_once "predicates/RequestOwnerIsNotTasksAssigneeFilter.php";
include_once "predicates/RequestDependsFilter.php";
include_once "sorts/IssueOwnerSortClause.php";
include_once "sorts/IssueFunctionSortClause.php";
include_once "sorts/IssueUnifiedTypeSortClause.php";

class Request extends MetaobjectStatable 
{
 	var $blocks_it, $links_it;
 	
 	function __construct( $registry = null ) 
 	{
		parent::__construct('pm_ChangeRequest', $registry, getSession()->getCacheKey());
 	}
 	
	function createIterator() 
	{
		return new RequestIterator( $this );
	}

	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'issues/board?mode=request&';
	}
	
	function getPlannedWorkload( $request_array )
 	{	
		$sql = " SELECT SUM(t.Planned) result " .
				"  FROM pm_Task t" .
				" WHERE t.ChangeRequest IN (".join(',', $request_array).") ";
				
		$it = $this->createSQLIterator($sql);
			
		return round($it->get('result'));
 	}

	function getRequestsAggByVersion( $version_name = '' )
	{
		global $project_it, $model_factory;
		
		$trace_class = getFactory()->getObject('RequestTraceTestCaseExecution')->getObjectClass();
		
		$sql = " SELECT t.Version, " .
			   "		SUM(t.Critical) Critical," .
			   "		SUM(t.Important) Important," .
			   "	    SUM(t.Other) Other, " .
			   "		SUM(CASE t.IssueType WHEN 'bug' THEN 1 ELSE 0 END) Bugs," .
			   "	    SUM(CASE t.IssueType WHEN 'bug' THEN 0 ELSE 1 END) Issues " .
			   "   FROM (" .
			   "		 SELECT t.Version, " .
			   "			    (CASE r.Priority WHEN 1 THEN 1 ELSE 0 END) Critical," .
			   "				(CASE r.Priority WHEN 2 THEN 1 ELSE 0 END) Important, " .
			   "			    (CASE r.Priority WHEN 1 THEN 0 WHEN 2 THEN 0 ELSE 1 END) Other, " .
			   "				(SELECT it.ReferenceName FROM pm_IssueType it WHERE it.pm_IssueTypeId = r.Type) IssueType" .
			   "           FROM pm_ChangeRequest r, " .
			   "			    pm_ChangeRequestTrace tr, ".
			   "				pm_TestCaseExecution e," .
			   "				pm_Test t " .
			   "          WHERE r.vpd IN ('".join("','",$this->getVpds())."')" .
			   "		    AND r.pm_ChangeRequestId = tr.ChangeRequest ".
			   "			AND tr.ObjectClass = '".$trace_class."' ".
			   "			AND tr.ObjectId = e.pm_TestCaseExecutionId" .
			   "		    AND e.Test = t.pm_TestId " .
			   "		 ) t" .
			   ( $version_name != '' ? " WHERE t.Version LIKE '".$version_name."%' " : " WHERE t.Version IS NOT NULL ").
			   " GROUP BY t.Version ";
			   			   
		return $this->createSQLIterator($sql);
	}
	
	function getRequestsAggByFunction()
	{
		global $project_it, $model_factory;
		
		$sql = " SELECT t.Function, " .
			   "		SUM(t.Critical) Critical," .
			   "		SUM(t.Important) Important," .
			   "	    SUM(t.Other) Other, " .
			   "		SUM(CASE t.IssueType WHEN 'bug' THEN 1 ELSE 0 END) Bugs," .
			   "	    SUM(CASE t.IssueType WHEN 'bug' THEN 0 ELSE 1 END) Issues " .
			   "   FROM (SELECT t.Function," .
			   "				(CASE t.Priority WHEN 1 THEN 1 ELSE 0 END) Critical," .
			   "				(CASE t.Priority WHEN 2 THEN 1 ELSE 0 END) Important, " .
			   "			    (CASE t.Priority WHEN 1 THEN 0 WHEN 2 THEN 0 ELSE 1 END) Other, " .
			   "				(SELECT it.ReferenceName FROM pm_IssueType it WHERE it.pm_IssueTypeId = t.Type) IssueType" .
			   "           FROM pm_ChangeRequest t " .
			   "          WHERE 1 = 1 ".$this->getVpdPredicate().$this->getFilterPredicate().
			   "		 ) t" .
			   " GROUP BY t.Function ";
			   			   
		return $this->createSQLIterator($sql);
	}
	
	function IsDeletedCascade( $object )
	{
		return false;
	}

	function getOrderStep()
	{
	    return 1;
	}
	
	function getDefaultAttributeValue( $attr_name )
	{
		if( $attr_name == 'Project' )
		{
			return getSession()->getProjectIt()->getId();
		}
			
		return parent::getDefaultAttributeValue( $attr_name );
	}
	
	function getAttributeUserName( $attr_name )
	{
		switch ( $attr_name )
		{
			default:
				return parent::getAttributeUserName( $attr_name );
		}
	}
	
	function addTraceAttribute( $attribute )
	{
	}
	
	function add_parms( $parms )
	{
		global $model_factory;
		
		if ( $parms['EstimationLeft'] == '' ) $parms['EstimationLeft'] = $parms['Estimation'];
		
		$request_id = parent::add_parms( $parms );
		
		if ( $parms['Question'] > 0 )
		{
			$trace = $model_factory->getObject('RequestTraceQuestion');
			$trace->add_parms( 
				array( 'ObjectId' => $parms['Question'],
					   'ObjectClass' => $trace->getObjectClass(),
					   'ChangeRequest' => $request_id )
			);
		}

		return $request_id;
	}
	
	function modify_parms( $object_id, $parms )
	{
		if ( $parms['Estimation'] != '' ) {
			$parms['EstimationLeft'] = $parms['Estimation'];
		}

		$req_it = $this->getExact($object_id);
		
		switch ( $parms['State'] )
		{
			case 'resolved':
				$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
				if ( $methodology_it->getEstimationStrategy() instanceof EstimationHoursStrategy && $methodology_it->HasTasks() )
				{
					if ( $req_it->get('Estimation') == '' ) {
						$parms['Estimation'] = $req_it->getPlannedDuration(); 					
 					}
				}
				break;
				
			default:
				if ( in_array($parms['State'], $this->getTerminalStates()) ) {
					$parms['EstimationLeft'] = 0;
				}
				break;
		}

		return parent::modify_parms( $object_id, $parms );
	}

	function delete( $id, $record_version = ''  )
	{
		$object_it = $this->getExact($id);

		$result = parent::delete( $id );

        if ( $object_it->getId() > 0 ) {
            DAL::Instance()->Query(" DELETE FROM pm_ChangeRequestTrace WHERE ChangeRequest = ".$object_it->getId());
        }

        return $result;
	}
}
 