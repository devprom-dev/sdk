<?php

class RequestIterator extends StatableIterator
{
 	var $task, $task_type;
 	
	function getDisplayName()
	{
	 	if ( $this->get('TypeName') != '' ) 
	 	{
	 		return $this->get('TypeName').': '.parent::getDisplayName();
	 	}
	 	elseif( $this->getId() > 0 )
	 	{
		 	return $this->object->getDisplayName().': '.parent::getDisplayName();
	 	}
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

 	function IsFinished() 
 	{
 		return in_array( $this->get_native('State'), $this->object->getTerminalStates() );
 	}

 	function IsImplemented()
 	{
 		return false;
 	}
 	
 	function IsBlocked()
 	{
 		$blocked_it = $this->getBlockedIt();
 		while ( !$blocked_it->end() )
 		{
			if ( $blocked_it->get('IsTerminal') == 'N' )
 			{
 				return true;
 			}
 			
 			$blocked_it->moveNext();
 		}
 		
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

		if ( $this->object->hasAttribute('Iterations') && $this->get('Iterations') != '' ) return $this->getRef('Iterations');

		if ( $this->object->hasAttribute('PlannedRelease') && $this->get('PlannedRelease') != '' ) return $this->getRef('PlannedRelease');
	}
		
	function getDuplicateIt()
	{
		global $model_factory;
		
		$it = $this->object->cacheLinks();
		$it->setStop( 'StopWord', $this->getId().',duplicates' );
		
		return $model_factory->_clone($it);
	}
	
	function getBlockedIt()
	{
		global $model_factory;
		
		$it = $model_factory->_clone($this->object->cacheBlocks());
		$it->setStop( 'StopWord', $this->getId().',blocked' );
		
		return $it;
	}

	function getDate( $date_field )
	{
		if ( !is_object($this->object->dates_it) )
		{
			$this->object->cacheDates();
		}

		$this->object->dates_it->moveTo( 'pm_ChangeRequestId', $this->getId() );
		return $this->object->dates_it->getDateFormat( $date_field );
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
	
 	function getStateExact() 
 	{
 		global $model_factory;
 		
 		$state = array();
 		
 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 		
		$stage_it = $this->getStageIt();
		
		if ( is_object($stage_it) )
		{
		    $titles = array();
		    
		    while( !$stage_it->end() ) { $titles[] = $stage_it->getDisplayName(); $stage_it->moveNext(); }
		    
		    $stage_name = join(', ', $titles);
		    
			$stage = $stage_it->object->getDisplayName().' '.$stage_name;
			
 			$pattern = text(811);

			if ( !is_object($this->version_settings) )
			{
				$version_settings = $model_factory->getObject('pm_VersionSettings');
				
		 		$this->version_settings = $version_settings->getAll();
			}
			
 			switch ( $stage_it->object->getClassName() )
 			{
 				case 'pm_Build':
 				case 'pm_Release':
 				    
		 			if ( $this->version_settings->get('UseIteration') == 'Y' )
		 			{
						$stage = $stage_name;
						
 						$pattern = text(810);
		 			}
		 			
		 			break;
		 			
 				case 'pm_Version':
		 			
 				    if ( $this->version_settings->get('UseRelease') == 'Y' )
		 			{
						$stage = $stage_name;
 						
						$pattern = text(810);
		 			}
		 			
		 			break;
 			}
		}

 		if ( $stage == "" )
		{
			$pattern = '%1';
		}

		switch ( $this->get('State') )
		{
			case 'planned':
	 			$tasks_state = $this->object->getAttributeType('Tasks') != '' ? $this->getRef('Tasks')->getStatesArray() : array();
	
	 			$implementation_completed = false;
	 			foreach ( $tasks_state as $task_state )
	 			{
	 				if ( (strtolower($task_state['type']) == 'development' || strtolower($task_state['type']) == 'support') 
	 					 && $task_state['progress'] == '100%' )
	 				{
	 					$implementation_completed = true;
	 					break;
	 				}
	 			}
	 			
				if ( $implementation_completed )
				{
					$message = translate('Реализовано');
				}
				else
				{
		 			$state_it = $this->getStateIt();
					$message = $state_it->getDisplayName();
				}
				
				$message = str_replace('%2', $stage, str_replace('%1', $message, $pattern) );
				array_push( $state, $message );
				
	 			$state = array_merge( $state, $tasks_state );
 				break;

			case 'release':
				if ( $this->get('PlannedRelease') > 0 )
		 		{
					$message = translate('В релизе').' '.$stage;
					array_push($state, $message );
		 		}
				break;
				
			default:
		 		$state_it = $this->getStateIt();
				$message = str_replace('%2', $stage, str_replace('%1', $state_it->getDisplayName(), $pattern));
	 			array_push( $state, $message );
	 			
	 			$state = array_merge( $state, $this->object->getAttributeType('Tasks') != '' ? $this->getRef('Tasks')->getStatesArray() : array() );
				break;
		}

 		return $state;
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
 		$task_it = $this->getRef('Tasks');
 		
 		while ( !$task_it->end() && $task_it->get('ChangeRequest') == $this->getId() )
 		{
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