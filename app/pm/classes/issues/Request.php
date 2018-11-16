<?php
include "RequestIterator.php";
include "persisters/IssueLinkedIssuesPersister.php";
include "persisters/RequestIterationsPersister.php";
include "predicates/RequestIterationFilter.php";
include "predicates/RequestAuthorFilter.php";
include "predicates/RequestEstimationFilter.php";
include "predicates/RequestTagFilter.php";
include "predicates/RequestTestResultPredicate.php";
include "predicates/RequestTaskTypePredicate.php";
include "predicates/RequestTaskStatePredicate.php";
include "predicates/RequestNonPlannedPredicate.php";
include "predicates/RequestReleasePredicate.php";
include "predicates/RequestDuplicatesOfFilter.php";
include "predicates/RequestImplementationFilter.php";
include "predicates/RequestDependencyFilter.php";
include "predicates/RequestFeatureFilter.php";
include "predicates/RequestFinishAfterPredicate.php";
include "predicates/RequestOwnerIsNotTasksAssigneeFilter.php";
include "predicates/RequestDependsFilter.php";
include "sorts/IssueOwnerSortClause.php";
include "sorts/IssueFunctionSortClause.php";
include "sorts/IssueUnifiedTypeSortClause.php";
include "RequestModelExtendedBuilder.php";

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
	
	function addTraceAttribute( $attribute )
	{
	}
	
	function add_parms( $parms )
	{
		if ( $parms['EstimationLeft'] == '' ) $parms['EstimationLeft'] = $parms['Estimation'];
		
		$request_id = parent::add_parms( $parms );
		
		if ( $parms['Question'] > 0 )
		{
			$trace = getFactory()->getObject('RequestTraceQuestion');
			$trace->add_parms( 
				array( 'ObjectId' => $parms['Question'],
					   'ObjectClass' => $trace->getObjectClass(),
					   'ChangeRequest' => $request_id )
			);
		}

		$this->updateUID($request_id);

		return $request_id;
	}
	
	function modify_parms( $object_id, $parms )
	{
		if ( $parms['Estimation'] != '' ) {
			$parms['EstimationLeft'] = $parms['Estimation'];
		}

		switch ( $parms['State'] )
		{
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

    function updateUID( $objectId )
    {
        if ( $objectId < 1 ) return;
        $uid = new ObjectUID();
        $sql = "UPDATE pm_ChangeRequest w SET w.UID = '".$uid->getObjectUidInt(get_class($this), $objectId)."' WHERE w.pm_ChangeRequestId = ".$objectId;
        DAL::Instance()->Query( $sql );
    }
}
 