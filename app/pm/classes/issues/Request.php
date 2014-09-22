<?php

include "RequestIterator.php";

include_once "persisters/IssueLinkedIssuesPersister.php";
include_once "persisters/RequestPlanningPersister.php";
include_once "persisters/RequestMilestonesPersister.php";
include_once "persisters/RequestIterationsPersister.php";
include_once "predicates/IssueOwnerUserPredicate.php";
include_once "predicates/RequestIterationFilter.php";
include_once "predicates/RequestAuthorFilter.php";
include_once "predicates/RequestSubmittedFilter.php";
include_once "predicates/RequestEstimationFilter.php";
include_once "predicates/RequestTagFilter.php";
include_once "predicates/RequestVersionFilter.php";
include_once "predicates/RequestTestResultPredicate.php";        
include_once "predicates/RequestStagePredicate.php";
include_once "predicates/RequestTaskTypePredicate.php";
include_once "predicates/RequestTaskStatePredicate.php";
include_once "predicates/RequestNonPlannedPredicate.php";
include_once "predicates/RequestReleasePredicate.php";
include_once "predicates/RequestDuplicatesOfFilter.php";
include_once "sorts/IssueOwnerSortClause.php";
include_once SERVER_ROOT_PATH."pm/classes/common/persisters/WatchersPersister.php";
include_once "validators/ModelValidatorIssueTasks.php";

class Request extends MetaobjectStatable 
{
 	var $blocks_it, $links_it;
 	
 	function __construct() 
 	{
		parent::__construct('pm_ChangeRequest');
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
	
	function getRequestsAggByTrace( $class_name )
	{
		$sql = " SELECT t.ObjectId, " .
			   "		SUM(t.Critical) Critical," .
			   "		SUM(t.Important) Important," .
			   "	    SUM(t.Other) Other, " .
			   "		SUM(CASE t.IssueType WHEN 'bug' THEN 1 ELSE 0 END) Bugs," .
			   "	    SUM(CASE t.IssueType WHEN 'bug' THEN 0 ELSE 1 END) Issues " .
			   "   FROM (SELECT e.ObjectId," .
			   "				(CASE t.Priority WHEN 1 THEN 1 ELSE 0 END) Critical," .
			   "				(CASE t.Priority WHEN 2 THEN 1 ELSE 0 END) Important, " .
			   "			    (CASE t.Priority WHEN 1 THEN 0 WHEN 2 THEN 0 ELSE 1 END) Other, " .
			   "				(SELECT it.ReferenceName FROM pm_IssueType it WHERE it.pm_IssueTypeId = t.Type) IssueType" .
			   "           FROM pm_ChangeRequest t," .
			   "			    pm_ChangeRequestTrace e " .
			   "          WHERE t.pm_ChangeRequestId = e.ChangeRequest" .
			   "			AND e.ObjectClass = '".$class_name."' ".
			   			  	    $this->getVpdPredicate().$this->getFilterPredicate().
			   "		 ) t" .
			   " GROUP BY t.ObjectId ";
		   
		return $this->createSQLIterator($sql);
	}
	
	function getProductBacklog( $limit = 0 )
	{
		$sort = $this->getSortClause();
		
		$sql = " SELECT (SELECT st.IsTerminal FROM pm_State st " .
			   "		  WHERE st.ObjectClass = 'request'" .
			   "		    AND st.VPD = t.VPD" .
			   "			AND st.ReferenceName = t.State )," .
			   "		(SELECT st.OrderNum FROM pm_State st " .
			   "		  WHERE st.ObjectClass = 'request'" .
			   "		    AND st.VPD = t.VPD" .
			   "			AND st.ReferenceName = t.State ), " .
			   "		t.Tags, ".$this->getRegistry()->getSelectClause('t').
			   "   FROM (SELECT t.*," .
			   "		  	    (SELECT GROUP_CONCAT(tg.Caption) " .
			   "		   		   FROM pm_RequestTag wt, Tag tg " .
			   "  		  		  WHERE wt.Request = t.pm_ChangeRequestId ".
			   "				    AND tg.TagId = wt.Tag ) Tags" .
			   "		   FROM pm_ChangeRequest t ".
			   "  		  WHERE 1 = 1 ".$this->getVpdPredicate().$this->getFilterPredicate().
			   "        ) t, Priority pr " .
			   "  WHERE t.Priority = pr.PriorityId ".
			   " ORDER BY ".($sort != '' ? $sort.", " : '')." t.pm_ChangeRequestId".
			   ($limit > 0 ? " LIMIT ".$limit : "");

		return $this->createSQLIterator($sql);
	}

	function getFeaturesBacklog()
	{
		$sql = " SELECT t.* " .
			   "   FROM pm_ChangeRequest t " .
			   "  WHERE 1 = 1 ".
			   $this->getFilterPredicate().$this->getVpdPredicate().
			   "  ORDER BY t.Function ASC ";

		return $this->createSQLIterator($sql);
	}

	function IsDeletedCascade( $object )
	{
		return false;
	}

	function getOrderStep()
	{
	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
	    
	    return is_object($methodology_it) && $methodology_it->get('IsRequestOrderUsed') == 'Y' ? 1 : parent::getOrderStep();
	}
	
	function getDefaultAttributeValue( $attr_name )
	{
		global $_REQUEST, $model_factory;
		
		$project_it = getSession()->getProjectIt();
		
		if( $attr_name == 'Project' && is_object($project_it) )
		{
			return $project_it->getId();
		}
			
		if( $attr_name == 'PlannedRelease' && is_object($project_it) )
		{
			if ( $_REQUEST['Release'] > 0 )
			{
				$iteration = $model_factory->getObject('Iteration');
				$iteration_it = $iteration->getExact($_REQUEST['Release']);
				
				return $iteration_it->get('Version');
			}
		}

		if( $attr_name == 'Author' )
		{
			return getSession()->getUserIt()->getId();
		}

		if( $attr_name == 'Owner' )
		{
		    $session = getSession();
		    
		    if ( is_a($session, 'PMSession') ) 
		    {
		        $it = $session->getParticipantIt();
		        
		        return $it->getId();
		    }
		}    
		
		if ( $attr_name == 'ClosedInVersion' && $_REQUEST['pm_ChangeRequestId'] > 0 )
		{
			$request = $model_factory->getObject('pm_ChangeRequest');
			
			$request_it = $request->getExact($_REQUEST['pm_ChangeRequestId']);

			$stage_it = $request_it->getStageIt();
			
			if ( is_object($stage_it) )
			{
				return $stage_it->getDisplayName();
			}
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
		global $model_factory;

		if ( $parms['Estimation'] != '' )
		{
			$parms['EstimationLeft'] = $parms['Estimation'];
		}
		
		$req_it = $this->getExact($object_id);
		
		switch ( $parms['State'] )
		{
			case 'resolved':
				if ( $parms['ClosedInVersion'] == '' )
				{
					$stage_it = $req_it->getStageIt();
					
					if ( is_object($stage_it) )
					{
						$parms['ClosedInVersion'] = $stage_it->getDisplayName();
					}
				}

				if ( $parms['ClosedInVersion'] == '' && $this->hasAttribute('PlannedRelease') )
				{
					$release_it = $req_it->getRef('PlannedRelease');
					
					$parms['ClosedInVersion'] = $release_it->getDisplayName();
				}
				
				$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
				
				if ( $methodology_it->getEstimationStrategy() instanceof EstimationHoursStrategy && $methodology_it->HasTasks() )
				{
					if ( $req_it->get('Estimation') == '' )
					{
						$parms['Estimation'] = $req_it->getPlannedDuration(); 					
 					}
				}
				break;
				
			default:
				if ( in_array($parms['State'], $this->getTerminalStates()) )
				{
					$parms['EstimationLeft'] = 0;
				}
				break;
		}

		return parent::modify_parms( $object_id, $parms );
	}
	
	function delete( $id )
	{
		global $model_factory;
		
		$object_it = $this->getExact($id);
		
		// delete attachments
		$attachment = $model_factory->getObject('pm_Attachment');
		$attachment->removeNotificator( 'EmailNotificator' );
		
		$attachment->addFilter( new AttachmentObjectPredicate($object_it) );
		$attachment_it = $attachment->getAll();
		
		while ( !$attachment_it->end() )
		{
			$attachment->delete( $attachment_it->getId() );
			$attachment_it->moveNext();
		}
		
		return parent::delete( $id );
	}
	
	function cacheDates()
	{
		$sql = " CREATE TEMPORARY TABLE tmp_RequestDate (" .
			   "	pm_ChangeRequestId INTEGER, CompleteDate DATE ) ";

		$this->createSQLIterator( $sql );

		$sql = " INSERT INTO tmp_RequestDate (pm_ChangeRequestId, CompleteDate) " .
		       " SELECT t.pm_ChangeRequestId, " .
			   "		IFNULL((SELECT m.MetricValueDate " .
			   " 		   FROM pm_VersionMetric m" .
			   "	      WHERE m.Version = r.pm_VersionId" .
			   "			AND m.Metric = 'EstimatedFinish'), r.FinishDate)" .
			   "   FROM pm_ChangeRequest t, pm_Version r" .
			   "  WHERE t.PlannedRelease = r.pm_VersionId";

		$this->createSQLIterator( $sql );
		
		$sql = " SELECT t.* FROM tmp_RequestDate t ";
		
		$this->dates_it = $this->createSQLIterator($sql);
		$this->dates_it->buildPositionHash( array('pm_ChangeRequestId') );
	}

 	function cacheBlocks()
	{
		global $model_factory;
		
		if ( is_object($this->blocks_it) )
		{
			return $this->blocks_it;
		}
		
		$sql = " SELECT CONCAT(r.RequestId, ',', r.ReferenceName) StopWord, " .
			   "	 	r.*, IFNULL(st.IsTerminal, 'N') IsTerminal " .
			   "   FROM (SELECT l.TargetRequest RequestId, r.*, " .
			   "				CASE t.ReferenceName ". 
			   "					WHEN 'blocks' THEN 'blocked' WHEN 'blocked' THEN 'blocks' ".
			   "				ELSE t.ReferenceName END ReferenceName " .
			   "		   FROM pm_ChangeRequest r," .
			   "				pm_ChangeRequestLink l," .
			   "				pm_ChangeRequestLinkType t" .
			   "		  WHERE l.LinkType = t.pm_ChangeRequestLinkTypeId " .
			   "    		AND r.pm_ChangeRequestId = l.SourceRequest" .
			   "		  UNION " .
			   "		 SELECT l.SourceRequest RequestId, r.*, t.ReferenceName" .
			   "		   FROM pm_ChangeRequest r," .
			   "				pm_ChangeRequestLink l," .
			   "				pm_ChangeRequestLinkType t" .
			   "		  WHERE l.LinkType = t.pm_ChangeRequestLinkTypeId " .
			   "    		AND r.pm_ChangeRequestId = l.TargetRequest" .
			   "		) r, " .
			   "		pm_State st " .
			   "  WHERE st.ObjectClass = 'request'" .
			   "	AND st.ReferenceName = r.State " .
			   "    AND st.VPD = r.VPD ".
			   "  ORDER BY 1 ASC ";

		$this->blocks_it = $this->createSQLIterator($sql);
		$this->blocks_it->buildPositionHash( array('StopWord') );
		
		return $this->blocks_it;
	}	

  	function cacheLinks()
	{
		global $model_factory;
		
		if ( is_object($this->links_it) )
		{
			return $this->links_it;
		}
		
		$sql = " SELECT r.RequestId, CONCAT(r.RequestId, ',', r.ReferenceName) StopWord, " .
			   "	 	r.*, st.Caption StateName, IFNULL(st.IsTerminal, 'N') IsTerminal " .
			   "   FROM (SELECT l.SourceRequest RequestId, r.*, t.ReferenceName " .
			   "		   FROM pm_ChangeRequest r," .
			   "				pm_ChangeRequestLink l," .
			   "				pm_ChangeRequestLinkType t" .
			   "		  WHERE l.LinkType = t.pm_ChangeRequestLinkTypeId " .
			   "    		AND r.pm_ChangeRequestId = l.TargetRequest" .
			   "		  UNION " .
			   "		 SELECT l.TargetRequest RequestId, r.*, t.ReferenceName" .
			   "		   FROM pm_ChangeRequest r," .
			   "				pm_ChangeRequestLink l," .
			   "				pm_ChangeRequestLinkType t" .
			   "		  WHERE l.LinkType = t.pm_ChangeRequestLinkTypeId " .
			   "    		AND r.pm_ChangeRequestId = l.SourceRequest" .
			   "		) r, " .
			   "		pm_State st " .
			   "  WHERE st.ObjectClass = 'request'" .
			   "	AND st.ReferenceName = r.State " .
			   "    AND st.VPD = r.VPD ".
			   "  ORDER BY 1 ASC, 2 ASC ";

		$this->links_it = $this->createSQLIterator($sql);
		$this->links_it->buildPositionHash( array('StopWord') );
		
		return $this->links_it;
	}	
}
 