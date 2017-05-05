<?php

class RequestIterator extends StatableIterator
{
 	var $task, $task_type;
 	
	function getDisplayName()
	{
	    $result = parent::getDisplayName();
	 	if ( $this->get('TypeName') != '' ) {
            $result = $this->get('TypeName').': '.$result;
	 	}
	 	return $result;
	}

	function getDisplayNameExt( $prefix = '' )
    {
        if ( $this->get('Deadlines') != '' && $this->get('DueWeeks') < 4 ) {
            $prefix .= '<span class="label '.($this->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'" title="'.$this->object->getAttributeUserName('DeliveryDate').'">';
            $prefix .= $this->getDateFormatShort('DeliveryDate');
            $prefix .= '</span> ';
        }
        $title = parent::getDisplayNameExt($prefix);
        if ( $this->get('ClosedInVersion') != '' ) {
            $title = ' <span class="badge badge-uid badge-info">'.$this->get('ClosedInVersion').'</span> ' . $title;
        }
        return $title;
    }

    function IsNew()
 	{
 		return $this->get_native('State') == 'submitted';
 	}

 	function IsResolved() 
 	{
 		return $this->get_native('State') == 'resolved';
 	}

 	function IsInScope() 
 	{
 		return $this->get_native('PlannedRelease') != '';
 	}

 	function IsFinished() {
 		return $this->get('StateTerminal') == 'Y';
 	}

 	function IsImplemented()
 	{
 		return false;
 	}
 	
	function IsTransitable()
	{
		return true;
	}
 	
 	function getTasksInIteration( $iteration_id ) 
 	{
		$sql_array = 
			'SELECT t.* ' .
			'  FROM pm_ChangeRequest r INNER JOIN ' .
			'			pm_Task t ON t.ChangeRequest = r.pm_ChangeRequestId '.
			' WHERE r.pm_ChangeRequestId = '.$this->getId().
			'   AND t.Release = '.$iteration_id.
			' ORDER BY t.RecordCreated ASC';
			
		if ( !is_object($this->task) ) 
		{
			$this->task = getFactory()->getObject('pm_Task');
		}

		return $this->task->createSQLIterator( $sql_array );
 	}

	function getStageIt()
	{
		global $model_factory;
		
		if ( $this->object->hasAttribute('ClosedInVersion') && $this->get('ClosedInVersion') != '' )
		{
			$stage = $model_factory->getObject('Stage');
			
			$stage_it = $stage->getExact( $this->get('ClosedInVersion') );
			
			return $stage_it->getObjectIt();
		}

		if ( $this->object->hasAttribute('Iteration') && $this->get('Iteration') != '' ) return $this->getRef('Iteration');
		if ( $this->object->hasAttribute('PlannedRelease') && $this->get('PlannedRelease') != '' ) return $this->getRef('PlannedRelease');
	}
		
	function getImplementationIds()
	{
		$result = array();
		$items = preg_split('/,/', $this->get('LinksWithTypes'));
		foreach( $items as $item ) {
			list($title, $id, $link_type, $state, $blocked) = preg_split('/:/', $item);
			if ( $link_type == 'implemented' ) {
				$result[] = $id;
			}
		}
		return $result;
	}

	function isSubmittedIn( $version )
	{
		global $project_it;
		
		$sql = " SELECT COUNT(1) cnt " .
			   "   FROM pm_ChangeRequestLink l, pm_ChangeRequest r " .
			   "  WHERE l.SourceRequest = ".$this->getId().
			   "    AND l.LinkType = 1" .
			   "    AND r.pm_ChangeRequestId = l.TargetRequest " .
			   "    AND r.SubmittedVersion LIKE '".$version."%' ";

		$it = $this->object->createSQLIterator($sql);
		return $it->get('cnt') > 0; 
	}
	
 	function getFilledStages()
 	{
		$sql_array =  
			'SELECT t.TaskType' .
			'  FROM pm_Task t ' .
			' WHERE t.ChangeRequest = '.$this->getId();
			
		if(!is_object($this->task)) 
		{
			$this->task = getFactory()->getObject('pm_Task');
		}
		
		$type_it = $this->task->createSQLIterator( $sql_array );
		
   		$types = array();
   		for($i = 0; $i < $type_it->count(); $i++) {
   			array_push($types, $type_it->get('TaskType'));
   			$type_it->moveNext();
   		}

		return $types;
 	}
 	 	
 	/*
 	 *  Returns the deadline when the issue will be resolved
 	 */
 	function getCompletionDeadline()
 	{
 		if ( $this->IsResolved() )
 		{
 			return '';
 		}
 		
 		$sql = 
 			" SELECT MAX(r.ReleaseNumber) LatestRelease, r.FinishDate " .
 			"   FROM pm_Task t INNER JOIN pm_Release r ON t.Release = r.pm_ReleaseId " .
 			"  WHERE t.ChangeRequest = ".$this->getId().
 			"  GROUP BY t.Release ";
 			
 		$it = $this->object->createSQLIterator( $sql );
 		
 		if ( $it->count() > 0 )
 		{
 			return translate('Завершение реализации').': '.
 				translate('итерация').' '.$it->get('LatestRelease').' ('.
 				$it->getDateFormat('FinishDate').')';
 		}
 		else
 		{
 			return '';
 		}
 	}

	/*
	 *  Returns the planned duration of all tasks related to the issue 
	 */ 	
 	function getPlannedDuration()
 	{
 		$duration = 0;
		if ( $this->object->getAttributeType('Tasks') == '' ) return $duration;

 		$task_it = $this->getRef('Tasks');
 		while ( !$task_it->end() && $task_it->get('ChangeRequest') == $this->getId() ) {
			$duration += $task_it->get("Planned");
 			$task_it->moveNext();
 		}	
 		return $duration;
 	} 	

 	 function IsBug() 
 	 {
 	 	$type_it = $this->getRef('Type');
 	 	return $type_it->get('ReferenceName') == 'bug';
 	 }
 	 
 	 function getEstimationInfo()
 	 {		
 	 	$total = 0;
		$estimated = 0;
		
		$nonestimated = array();
		
		$this->moveFirst();
		for( $i = 0; $i < $this->count(); $i++ )
		{
			if ( $this->get("Estimation") != '' )
			{
				$estimated++;
			}
			else
			{
				array_push($nonestimated, $this->getId());
			}
			
			$total += $this->get("Estimation");
			$this->moveNext();
		}
		
		/*
		if ( count($nonestimated) > 0 )
		{
			$sql = " SELECT SUM(t.Planned) Planned " .
				   "   FROM pm_Task t" .
				   "  WHERE t.ChangeRequest IN (".join(',', $nonestimated).") " .
				   "  GROUP BY t.ChangeRequest ";
				   
			$it = $this->object->createSQLIterator($sql);
			
			while ( !$it->end() )
			{
				$total += $it->get('Planned');
				$estimated++;
				
				$it->moveNext();
			}
		}
		*/
		
		return array(round($total, 1), round($estimated/$this->count()*100, 1));
 	 }
 	 
 	 function getPlannedWorkload()
 	 {	
 	 	$ids = array();
		$this->moveFirst();
		
		for( $i = 0; $i < $this->count(); $i++ )
		{
			array_push($ids, $this->getId());
			$this->moveNext();
		}
		
		return $this->object->getPlannedWorkload( $ids );
 	 }

 	 function getImplementors()
 	 {
 	 	$sql = "SELECT p.* " .
 	 		   "  FROM pm_Participant p INNER JOIN pm_Task t ON t.Assignee = p.SystemUser" .
 	 		   "       INNER JOIN pm_ChangeRequest r ON t.ChangeRequest = r.pm_ChangeRequestId " .
 	 		   " WHERE r.pm_ChangeRequestId = ".$this->getId();
 	 		   
 	 	return getFactory()->getObject('pm_Participant')->createSQLIterator($sql);
 	 }
 	 
 	 function getBuild()
 	 {
 	 	if ( $this->get('Build') > 0 )
 	 	{
 	 		$build_it = $this->getRef( 'Build' );
 	 		return $build_it->getFullNumber();
 	 	}
 	 	else
 	 	{
 	 		return '';
 	 	}
 	 }

 	function getOutsourcingIt()
 	{
 		return getFactory()->getObject('co_IssueOutsourcing')->getCurrentIt($this);
 	}

 	function getSuggestionIt()
 	{
 		return getFactory()->getObject('co_OutsourcingSuggestion')->getForIssueIt($this);
 	}
 	
 	function getChangesIt( $limit = 0 )
 	{
 		global $model_factory;
 		
		$changes = $model_factory->getObject('ChangeLog');
		$changes->defaultsort = 'RecordCreated DESC';

		$sql = " SELECT t.* " .
			   "  FROM ( SELECT l.* " .
					   "   FROM ObjectChangeLog l " .
					   "  WHERE l.ObjectId = ".$this->getId().
					   "    AND l.ClassName = 'pm_ChangeRequest' ) t " .
			   "  ORDER BY t.RecordCreated DESC ".
			   ( $limit > 0 ? " LIMIT ".$limit : "");
			   
		return $changes->createSQLIterator($sql);
 	}
 	
 	function getProgress()
 	{
 		global $model_factory;
 		
 		$task = $model_factory->getObject('pm_Task');
 		$types_map = $task->getTypesMap();
 		
 		$ids = array();
 		while ( !$this->end() )
 		{
 			array_push($ids, $this->getId());
 			$this->moveNext();
 		}
 		
 		if ( count($ids) < 1 )
 		{
 			array_push($ids, 0);
 		}
 		
		$sql = " SELECT COUNT(o.ChangeRequest) Total, SUM(o.Resolved) Resolved, o.Kind " .
			   "  FROM ( SELECT t.ChangeRequest, (CASE t.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved, " .
			   		   "		CASE t.TaskType " .
			   		   "			WHEN '".$types_map['support']."' THEN 'D'" .
			   		   "		 	WHEN '".$types_map['development']."' THEN 'D'" .
			   		   "		 	WHEN '".$types_map['analysis']."' THEN 'A'" .
			   		   "		 	WHEN '".$types_map['design']."' THEN 'A'" .
			   		   "		 	WHEN '".$types_map['documenting']."' THEN 'H'" .
			   		   "		 	WHEN '".$types_map['testing']."' THEN 'T'" .
			   		   " 		END Kind " .
		 			   "   FROM pm_Task t" .
		 			   "  UNION ".
		 		       " SELECT r.pm_ChangeRequestId, CASE r.State WHEN 'resolved' THEN 1 ELSE 0 END Resolved, 'R' Kind " .
		 			   "   FROM pm_ChangeRequest r ) o " .
			   " WHERE o.ChangeRequest IN (".join(',', $ids).")" .
			   " GROUP BY o.Kind ";

 		$it = $this->object->createSQLIterator( $sql );
 		$result = array();
 		
 		while ( !$it->end() )
 		{
 			$result[$it->get('Kind')] = array( $it->get('Total'), $it->get('Resolved') );

 			$it->moveNext();
 		}

 		return $result;
 	}
}