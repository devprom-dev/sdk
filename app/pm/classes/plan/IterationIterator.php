<?php

define(RESULT_FAILED, 'Провален');

class IterationIterator extends OrderedIterator
{
	function IsFinished() 
	{
		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get('EstimatedFinishDate')."') Offset ";
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Offset') > 0;
	}
	
	function IsDraft()
	{
		return $this->get('IsDraft') == 'Y';
	}
	
	function IsCurrent()
	{
		global $model_factory;
		
		$task = $model_factory->getObject('pm_Task');
		
		$task->addFilter( new StatePredicate('notresolved') );
		$task->addFilter( new FilterAttributePredicate('Release', $this->getId()) );

		return $task->getRecordCount() > 0;
	}

	function IsFuture()
	{
		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get_native('StartDate')."') diff ";

		$it = $this->object->createSQLIterator( $sql );
		return $it->get('diff') < 0;
	}
	
	function IsEmpty()
	{
		global $model_factory;
		$task = $model_factory->getObject('pm_Task');
		
		return $task->getByRefArrayCount(
			array('Release' => $this->getId() ) ) < 1;
	}
	
	function getCapacity() 
	{
	    return $this->get('Capacity');
	}

	function getCurrentCapacity() 
	{
	    return $this->get('ActualDurationInWorkingDays');
	}

	function getPlannedCapacity() 
	{
	    return $this->get('PlannedCapacity');
	}
	
	function getLeftCapacity() 
	{
	    return $this->get('LeftCapacityInWorkingDays');
	}

	function getTotalWorkload( $predicates = array() ) 
	{
		global $model_factory;
		
		$task = $model_factory->getObject('pm_Task');
		$task->setVpdContext($this);

		$task->addFilter( new FilterAttributePredicate('Release', $this->getId()) );
		$task->addFilter( new StatePredicate('notresolved') );
		
		foreach( $predicates as $predicate ) $task->addFilter( $predicate );
		
		$project_it = $this->getRef('Project');
		$methodology_it = $project_it->getMethodologyIt();
		 
		if ( $methodology_it->TaskEstimationUsed() )
		{
			$sum_aggregate = new AggregateBase( 'Release', 'LeftWork', 'SUM' );
		}
		else
		{
			$sum_aggregate = new AggregateBase( 'Release', 'pm_TaskId', 'COUNT' );
		}
		
		$task->addAggregate( $sum_aggregate );
		$task_it = $task->getAggregated();
		
		return $task_it->get( $sum_aggregate->getAggregateAlias() );
	}

	function getTotalHours() 
	{
		$sql = 'SELECT IFNULL(SUM(IFNULL(Fact + LeftWork, Planned)), 0) Workload FROM pm_Task t '.
			   ' WHERE t.Release = '.$this->getId();
		
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Workload');
	}

	function getSpentHours() 
	{
		$sql = 'SELECT IFNULL(SUM(t.Planned - t.LeftWork), 0) Workload FROM pm_Task t '.
			   ' WHERE t.Release = '.$this->getId().' AND t.Assignee IS NOT NULL ';
		
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Workload');
	}

	function getSpentHoursByParticipant( $user_id ) 
	{
		$sql = 'SELECT IFNULL(SUM(Planned - LeftWork), 0) Workload FROM pm_Task t '.
			   ' WHERE t.Release = '.$this->getId().
			   '   AND t.Assignee = '.$user_id;
		
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Workload');
	}

	function getPlannedTotalWorkload( $predicates = array() ) 
	{
		global $model_factory;

		$task = $model_factory->getObject('pm_Task');
		$task->setVpdContext($this);

		$task->addFilter( new FilterAttributePredicate('Release',$this->getId()) );
		foreach( $predicates as $predicate ) $task->addFilter( $predicate );
		
		$project_it = $this->getRef('Project');
		$methodology_it = $project_it->getMethodologyIt();
		
		if ( $methodology_it->TaskEstimationUsed() )
		{ 
			$sum_aggregate = new AggregateBase( 'Release', 'Planned', 'SUM' );
		}
		else
		{
			$sum_aggregate = new AggregateBase( 'Release', 'pm_TaskId', 'COUNT' );
		}
		$task->addAggregate( $sum_aggregate );
		$task_it = $task->getAggregated();
		
		$result = $task_it->get( $sum_aggregate->getAggregateAlias() );

		if ( $methodology_it->TaskEstimationUsed() )
		{
			// append spent time on iteration if tasks were moved
			$activity = $model_factory->getObject('pm_Activity');
			$activity->setVpdContext($this);
			
			$activity->addFilter( new ActivityIterationOnlyPredicate($this->getId()) );
			
			$spent_aggregate = new AggregateBase( 'Iteration', 'Capacity', 'SUM' );
			$activity->addAggregate( $spent_aggregate );
			$activity_it = $activity->getAggregated();
			
			$result += $activity_it->get( $spent_aggregate->getAggregateAlias() );

			// subtract spent time on other iterations than current one
			$activity = $model_factory->getObject('pm_Activity');
			$activity->setVpdContext($this);
			
			$activity->addFilter( new ActivityOtherIterationsPredicate($this->getId()) );
			
			$spent_aggregate = new AggregateBase( 'Iteration', 'Capacity', 'SUM' );
			$activity->addAggregate( $spent_aggregate );
			$activity_it = $activity->getAggregated();
			
			$result = max($result - $activity_it->get( $spent_aggregate->getAggregateAlias() ), 0);
		}
		
		return $result;
	}

	function getEstimation()
	{
		global $model_factory;
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->setVpdContext($this);
		
		$request->addFilter( new RequestIterationFilter($this->getId()) );
				
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		
		$data = $strategy->getEstimation( $request );
		
		return array_shift($data); 
	}

	function getCompletedEstimation()
	{
		global $model_factory;
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->setVpdContext($this);
		
		$request->addFilter( new RequestIterationFilter($this->getId()) );
		$request->addFilter( new RequestVersionFilter($this->getDisplayName()) );
		$request->addFilter( new StatePredicate('terminal') );
		
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		
		$data = $strategy->getEstimation( $request );
		
		return array_shift($data); 
	}
	
	function getInitialBurndownMetrics()
	{
		$project_it = $this->getRef('Project');
		
		$sql =  "SELECT m.PlannedWorkload " .
		 		"  FROM pm_Release r " .
		 		"		LEFT OUTER JOIN pm_ReleaseMetrics m " .
		 		"			ON r.pm_ReleaseId = m.Release " .
		 		" WHERE r.pm_ReleaseId = ".$this->getId().
				"   AND m.TaskType IS NULL ".
		 		" ORDER BY m.SnapshotDays ASC LIMIT 1 ";

		$it = $this->object->createSQLIterator( $sql );
		
		$duration = $this->get('PlannedCapacity') > 0 ? round($this->get('PlannedCapacity'), 0) : $this->getCapacity();
		$capacity = $it->get('PlannedWorkload') > 0 ? $it->get('PlannedWorkload') : $this->getPlannedTotalWorkload();
		
		if ( $project_it->getMethodologyIt()->HasFixedRelease() && $duration > 0 ) {
			$velocity = $this->getVelocity() / $duration;
		}
		else {
			$velocity = $this->getVelocity();
		}
		return array( $duration, $capacity, $velocity ); 
	}
	
	function getEstimatedBurndownMetrics()
	{
		list( $in_duration, $in_capacity, $in_velocity ) = $this->getInitialBurndownMetrics();

		$duration = $this->getLeftCapacity();
		
		$capacity = $in_velocity > 0 ? ceil($duration * $in_velocity) : 0;
				
		return array( $duration, $capacity, $in_velocity );
	}
	
	function getPlannedWorkSpeed() 
	{
		$sql = 'SELECT ROUND(SUM(r.Capacity)) Capacity '.
			   '  FROM pm_Participant p, pm_ParticipantRole r '.
			   ' WHERE r.Project = '.$this->get('Project').
			   '   AND r.Participant = p.pm_ParticipantId '.
			   "   AND p.IsActive = 'Y' ";
			   
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Capacity');
	}

	function getPlannedWorkSpeedByTasks() 
	{
		$sql = 'SELECT ROUND(SUM(r.Capacity)) Capacity '.
			   '  FROM pm_Participant p, pm_ParticipantRole r  '.
			   ' WHERE r.Project = '.$this->get('Project').
			   '   AND r.Participant = p.pm_ParticipantId '.
			   "   AND p.IsActive = 'Y' ".
			   "   AND EXISTS (SELECT 1 FROM pm_Task t, pm_TaskType tp" .
			   "				WHERE r.ProjectRole = tp.ProjectRole " .
			   "				  AND tp.pm_TaskTypeId = t.TaskType" .
			   "			      AND t.Release = ".$this->getId().") ";
			   
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Capacity');
	}

	function getLeftWorkParticipant( $part_it ) 
	{
		if ( $part_it->getId() < 1 ) return 0;
		
		$sql = "SELECT SUM(IFNULL(t.LeftWork, 0)) leftwork ".
			   "  FROM pm_Task t, pm_Participant p ".
			   " WHERE t.State <> 'resolved' ".
			   "   AND p.pm_ParticipantId = ".$part_it->getId().
			   "   AND p.SystemUser = t.Assignee ".
  		 	   "   AND t.Release = " .$this->getId();

		$it = $this->object->createSQLIterator( $sql );			   
		return $it->get('leftwork');
	}
	
	function getTasksByPriorityByType( $task_type_it )
	{
		$result = array();

		$sql = 'SELECT Priority, IFNULL(COUNT(1), 0) FROM pm_Task t '.
			   ' WHERE t.Release = '.$this->getId().' AND t.State <> \'resolved\' '.
			   '   AND t.TaskType = '.$task_type_it->getId().' GROUP BY t.Priority ORDER BY t.Priority';
		
		$r2 = DAL::Instance()->Query($sql);

		$sql = 'SELECT Priority, COUNT(1) FROM pm_Task t '.
			   ' WHERE t.Release = '.$this->getId().
			   '   AND t.TaskType = '.$task_type_it->getId().' GROUP BY t.Priority ORDER BY t.Priority';
		
		$r3 = DAL::Instance()->Query($sql);

		$prev_priority = 0;
		$state_data = mysql_fetch_array($r2);
		
		for($i = 0; $i < mysql_num_rows($r3); $i++) {
			$count_data = mysql_fetch_array($r3);
			if($count_data[0] == $state_data[0]) {
				array_push( $result, array( $count_data[0], $state_data[1], $count_data[1] ) );
				$state_data = mysql_fetch_array($r2);
			}
		}
		
		return $result;
	}

	function getTasksByType( $task_type_it )
	{
		$result = array();

		$sql = 'SELECT IFNULL(COUNT(1), 0) FROM pm_Task t '.
			   ' WHERE t.Release = '.$this->getId().' AND t.State = \'resolved\' '.
			   '   AND t.TaskType = '.$task_type_it->getId();
		
		$r2 = DAL::Instance()->Query($sql);

		$sql = 'SELECT COUNT(1) FROM pm_Task t '.
			   ' WHERE t.Release = '.$this->getId().
			   '   AND t.TaskType = '.$task_type_it->getId();
		
		$r3 = DAL::Instance()->Query($sql);

		$state_data = mysql_fetch_array($r2);
		$count_data = mysql_fetch_array($r3);
		
		return array( $state_data[0], $count_data[0] );
	}

	function getParticipantInvolvement( $user_id, $stage )
	{
		global $model_factory;
		
		$task = $model_factory->getObject('pm_Task');
		
		$task->setVpdContext($this);
		
		$types = array();
		
		switch ( $stage )
		{
			case 'development':
				$types[] = 'support';
				$types[] = 'development';
				break;
				
			case 'testing':
				$types[] = 'testing';
				break;
				
			default:
				return 0;
		}
		
		$task->addFilter( new FilterAttributePredicate('TaskType', join(',', $types)) );
		
		$sql = 
			" SELECT SUM(t.Planned) TotalWorkload" .
			"   FROM pm_Task t" .
			"  WHERE t.Release = ".$this->getId().$task->getFilterPredicate('t');
			
		$it_total = $this->object->createSQLIterator($sql);
		
		$sql = 
			" SELECT SUM(t.Planned) ParticipantWorkload" .
			"   FROM pm_Task t" .
			"  WHERE t.Release = ".$this->getId().$task->getFilterPredicate('t').
			"    AND t.Assignee = ".$user_id;

		$it_part = $this->object->createSQLIterator($sql);

		if ( $it_total->get('TotalWorkload') > 0 )
		{
			return round($it_part->get('ParticipantWorkload') / $it_total->get('TotalWorkload') * 100, 0);
		}
		else
		{
			return 0;
		}
	}
		
	function getPrevSiblingRelease()
	{	
		$sql = 'SELECT * FROM pm_Release t ' .
			   ' WHERE t.Version = '.($this->get('Version') > 0 ? $this->get('Version') : 'NULL').
			   '   AND TO_DAYS(t.StartDate) < TO_DAYS(\''.$this->get_native('StartDate')."') ".
			   ' ORDER BY t.StartDate DESC  ';

		return $this->object->createSQLIterator($sql);
	}

	function getVelocity()
	{
		return $this->get('Velocity'); 
	}
	
	function getRecentBuildIt()
	{
		global $model_factory;
		
		$sql = 
			" SELECT b.* " .
			"   FROM pm_Build b" .
			"  WHERE b.Release = ".$this->getId().
			"  ORDER BY b.Caption DESC" .
			"  LIMIT 1";
			
		$build = $model_factory->getObject('pm_Build');
		return $build->createSQLIterator( $sql );
	}
	
	function getBuildsIt()
	{
		global $model_factory;
		
		$sql = 
			" SELECT b.* " .
			"   FROM pm_Build b" .
			"  WHERE b.Release = ".$this->getId().
			"  ORDER BY b.Caption DESC";
			
		$build = $model_factory->getObject('pm_Build');
		return $build->createSQLIterator( $sql );
	}

	function storeMetricsSnapshot()
	{
		global $model_factory;
		
		$sql = " SELECT GREATEST(LEAST(TO_DAYS(NOW()), TO_DAYS(r.FinishDate)), TO_DAYS(r.StartDate)) TodayDays, " .
			   "        FinishDate " .
			   "   FROM pm_Release r " .
			   "  WHERE r.pm_ReleaseId = ".$this->getId();
			   
		$it = $this->object->createSQLIterator($sql);
			   
		$metrics = getFactory()->getObject('pm_ReleaseMetrics');
			
		$metrics_it = $metrics->getByRefArray(
			array( 'Release' => $this->getId(),
				   'SnapshotDays' => $it->get("TodayDays") ) 
			);

		if ( $this->IsFinished() )
		{
			$metric_date = $it->get('FinishDate');
		}
		else
		{
			$metric_date = 'NOW()';
		}
		
		$workload = $this->getTotalWorkload();
		$plannedworkload = $this->getPlannedTotalWorkload();
		$skip_plannedworkload = $metrics_it->count() == 1 && $plannedworkload == 0; 

		while ( !$metrics_it->end() )
		{
			$metrics_it->delete();
			$metrics_it->moveNext();
		}

		$metrics->add_parms( array( 
			'Release' => $this->getId(),
		    'SnapshotDays' => $it->get("TodayDays"),
		    'SnapshotDate' => $metric_date,
		    'Workload' => $workload,
		    'PlannedWorkload' => !$skip_plannedworkload 
				? $plannedworkload : $it->get('PlannedWorkload') 
		));

		$task = $model_factory->getObject('Task');
		
		$task_type = $model_factory->getObject('TaskTypeBase');
		$task_type->addFilter( new TaskTypeBaseIterationRelatedPredicate($this->getId()) );

		$task_type_it = $task_type->getAll();
		while ( !$task_type_it->end() )
		{
			$predicates = array ( new TaskTypeBasePredicate( $task_type_it->getId() ) );
			
			$workload = $this->getTotalWorkload($predicates);
			$plannedworkload = $this->getPlannedTotalWorkload($predicates);
			
			$metrics->add_parms( array( 
				'Release' => $this->getId(),
				'TaskType' => $task_type_it->getId(),
				'SnapshotDays' => $it->get("TodayDays"),
				'SnapshotDate' => $metric_date,
				'Workload' => $workload,
		    	'PlannedWorkload' => !$skip_plannedworkload 
					? $plannedworkload : $it->get('PlannedWorkload')
			));
				
			$task_type_it->moveNext();
		}
	}

	function resetBurndown()
	{
		global $model_factory;
		
		$sql = 
			" DELETE FROM pm_ReleaseMetrics " .
			"  WHERE pm_ReleaseMetrics.Release = ".$this->getId().
			"    AND pm_ReleaseMetrics.SnapshotDays NOT BETWEEN TO_DAYS('".$this->get_native('StartDate')."')".
						" AND TO_DAYS('".$this->get_native('FinishDate')."')";
			
		DAL::Instance()->Query($sql);			
		
		$sql = 
			" DELETE FROM pm_ReleaseMetrics " .
			"  WHERE pm_ReleaseMetrics.Release = ".$this->getId().
			"    AND pm_ReleaseMetrics.SnapshotDays > TO_DAYS(NOW())";
			
		DAL::Instance()->Query($sql);			
		
		$sql = " SELECT r.* " .
			   "   FROM pm_ReleaseMetrics r " .
			   "  WHERE r.Release = ".$this->getId().
			   "    AND r.SnapshotDays = (SELECT MIN(r2.SnapshotDays) FROM pm_ReleaseMetrics r2 " .
			   "						   WHERE r2.Release = ".$this->getId().") ";
			   
		$task = $model_factory->getObject('Task');
		
		$metrics = $model_factory->getObject('pm_ReleaseMetrics');
		
		$it = $metrics->createSQLIterator($sql);
		
		while ( !$it->end() )
		{
			$predicates = $it->get('TaskType') < 1 ? array() 
				: array ( new TaskTypeBasePredicate($it->get('TaskType')) );

			$metrics->modify_parms( $it->getId(), array ( 
				'PlannedWorkload' => $this->getPlannedTotalWorkload($predicates) 
			));
			
			$it->moveNext();
		}

		$this->storeMetrics();
	}
	
	function storeMetrics()
	{
		$release_it = $this->getRef('Version');

		$estimation = $this->getCompletedEstimation();
		
		// calculate velocity		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease() )
		{
			$velocity = round($estimation, 1);
		}
		else
		{
		    $capacity = $this->getCurrentCapacity();
		    
			$velocity = $capacity > 0 ? round($estimation / $this->getCurrentCapacity(), 1) : 0;
		}

		if ( $velocity <= 0 )
		{
			$velocity = $release_it->get('InitialVelocity');
		}

		$planned_speed = $this->getPlannedWorkSpeedByTasks();
		if ( $planned_speed > 0 )
		{
			$efficiency = round($velocity / $planned_speed * 100, 0);
		}
		else
		{
			$efficiency = 0;
		}

		$metrics = array (
			'Velocity' => $velocity,
			'Efficiency' => $efficiency
		);
		
		$date_metrics = array (
			'EstimatedStart' => $this->_getEstimatedStart(false),
			'EstimatedFinish' => $this->_getEstimatedFinish(false)
		);
		 
		if ( $this->IsCurrent() )
		{
			$metrics['ReleaseEstimation'] = $release_it->getPlannedTotalWorkload();
		}
		
		$metrics['IterationEstimation'] = $estimation;
		
		$metric = getFactory()->getObject('pm_IterationMetric');
		$metric->setNotificationEnabled(false);
		
		DAL::Instance()->Query("LOCK TABLES pm_IterationMetric WRITE, pm_IterationMetric t WRITE");

		$sql = " DELETE FROM pm_IterationMetric WHERE Iteration = ".$this->getId();
		
		DAL::Instance()->Query($sql);			

		foreach ( $metrics as $key => $value )
		{
			$metric->add_parms(
				array( 'Iteration' => $this->getId(),
					   'Metric' => $key,
					   'MetricValue' => $value ) );
		}
		
		foreach ( $date_metrics as $key => $value )
		{
			$metric->add_parms(
				array( 'Iteration' => $this->getId(),
					   'Metric' => $key,
					   'MetricValueDate' => $value ) );
		}
		
		DAL::Instance()->Query("UNLOCK TABLES");

		$part_it = getFactory()->getObject('User')->getRegistry()->Query(
			array (
				new UserWorkerPredicate(),
				new UserParticipatesDetailsPersister()
			)
		);
		$release_capacity = $this->getCurrentCapacity();
		
		$metrics = array();
		while ( !$part_it->end() )
		{
			$required_capacity = $part_it->get('Capacity') * $release_capacity;
			$spent_hours = $this->getSpentHoursByParticipant( $part_it->getId() );

			if ( $required_capacity > 0 )
			{
				$efficiency = ($spent_hours / $required_capacity) * 100;
			}
			else
			{
				$efficiency = 0;
			}
			
			$development_involvement = 
				$this->getParticipantInvolvement($part_it->getId(), 'development');
				
			$testing_involvement = 
				$this->getParticipantInvolvement($part_it->getId(), 'testing');
				
			$metrics[$part_it->getId()] = array (
				'RequiredCapacity' => $required_capacity,
				'SpentHours' => $spent_hours,
				'Efficiency' => $efficiency,
				'Velocity' => $release_capacity > 0 ? $spent_hours / $release_capacity : 0,
				'DevelopmentInvolvement' => $development_involvement,
				'TestingInvolvement' => $testing_involvement
				);
			
			$part_it->moveNext();
		}

		$metric = getFactory()->getObject('pm_ParticipantMetrics');
		$metric->setNotificationEnabled(false);
		
		DAL::Instance()->Query("LOCK TABLES pm_ParticipantMetrics WRITE, pm_ParticipantMetrics t WRITE");

		$sql = " DELETE FROM pm_ParticipantMetrics WHERE Iteration = ".$this->getId();
		
		DAL::Instance()->Query($sql);			
		
		foreach ( $metrics as $participant_id => $values )
		{
			foreach ( $values as $key => $value )
			{
				$metric->add_parms(
					array('Iteration' => $this->getId(),
						  'Participant' => $participant_id,
						  'Metric' => $key,
						  'MetricValue' => $value) );
			}
		}
		
		DAL::Instance()->Query("UNLOCK TABLES");
	}
	
	function getMetricsDate()
	{
		$sql = " SELECT MAX(m.RecordModified) LastDate " .
			   "   FROM pm_IterationMetric m " .
			   "  WHERE m.Iteration = ".$this->getId();
			   
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->getDateTimeFormat('LastDate');
	}

	function getSeparateTaskIt()
	{
		$sql = 'SELECT * FROM pm_Task t ' .
			   ' WHERE t.Release = ' .$this->getId().
			   '   AND t.ChangeRequest IS NULL '.
			   ' ORDER BY t.Priority, t.OrderNum, t.State, t.TaskType, t.RecordCreated ASC';

		return getFactory()->getObject('pm_Task')->createSQLIterator($sql);
	}
 
 	function getProgress()
	{
		global $model_factory;
		
		$task = $model_factory->getObject('pm_Task');
		$progress = array();
		
 		$sql = " SELECT COUNT(1) Total, SUM(CASE t.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved, 'D' Kind " .
 			   "   FROM pm_Task t, pm_TaskType tt " .
 			   "  WHERE t.Release = ".$this->getId().
 			   "    AND t.TaskType = tt.pm_TaskTypeId " .
 			   "	AND tt.ReferenceName IN ('support','development')".
 			   "  UNION ".
 		       " SELECT COUNT(1) Total, SUM(CASE t.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved, 'A' Kind " .
 			   "   FROM pm_Task t, pm_TaskType tt " .
 			   "  WHERE t.Release = ".$this->getId().
 			   "    AND t.TaskType = tt.pm_TaskTypeId " .
 			   "	AND tt.ReferenceName IN ('analysis','design')".
 			   "  UNION ".
 		       " SELECT COUNT(1) Total, SUM(CASE t.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved, 'H' Kind " .
 			   "   FROM pm_Task t, pm_TaskType tt " .
 			   "  WHERE t.Release = ".$this->getId().
 			   "    AND t.TaskType = tt.pm_TaskTypeId " .
 			   "	AND tt.ReferenceName IN ('documenting')".
 			   "  UNION ".
 		       " SELECT COUNT(1) Total, SUM(CASE t.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved, 'T' Kind " .
 			   "   FROM pm_Task t, pm_TaskType tt " .
 			   "  WHERE t.Release = ".$this->getId().
 			   "    AND t.TaskType = tt.pm_TaskTypeId " .
 			   "	AND tt.ReferenceName IN ('testing')".
 			   "  UNION ".
		 	   " SELECT COUNT(1), SUM(CASE r.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved, 'R' Kind " .
		 	   "   FROM pm_ChangeRequest r" .
		 	   "  WHERE EXISTS (SELECT 1 FROM pm_Task t " .
		 	   "				 WHERE t.ChangeRequest = r.pm_ChangeRequestId " .
		 	   "				   AND t.Release = ".$this->getId().") ";
 		
 		$it = $this->object->createSQLIterator( $sql );
		
 		while ( !$it->end() )
 		{
 			$progress[$it->get('Kind')][0] += $it->get('Total');
 			$progress[$it->get('Kind')][1] += $it->get('Resolved');

 			$it->moveNext();
 		}

		return $progress;
	}

	function getStartDate()
	{
		return $this->getDateFormat('StartDate');
	}
	
	function getFinishDate()
	{
		return $this->getDateFormat('FinishDate');
	}
	
	function _getEstimatedFinish( $formatted = true, $format = '')
	{
		global $project_it;
		
		if ( $format == '' )
		{
			$format = getSession()->getLanguage()->getDateFormat();
		}

		list( $left_days, $est_capacity, $est_velocity ) = $this->getEstimatedBurndownMetrics();
		
		if ( $left_days > 0 )
		{
			$sql = " SELECT FROM_DAYS(TO_DAYS(GREATEST(NOW(), r.StartDate)) + ".$left_days." - 1) dt".
				   "   FROM pm_Release r " .
				   "  WHERE r.pm_ReleaseId = ".$this->getId();

			$it = $this->object->createSQLIterator($sql);
			
			if ( $formatted )
			{
				return getSession()->getLanguage()->getDateUserFormatted( $it->get('dt'), $format);
			}
			else
			{
				return $it->get('dt');
			}
		}
		else
		{
			if ( $formatted )
			{
				return $this->getDateFormatUser( 'FinishDate', $format);
			}
			else
			{
				return $this->get_native('FinishDate');
			}
		}
	}

	function _getEstimatedStart( $formatted = true, $format = '')
	{
		if ( $format == '' )
		{
			$format = getSession()->getLanguage()->getDateFormat();
		}

		$prev_it = $this->getPrevSiblingRelease();

		if ( $prev_it->count() > 0 )
		{
			$prev_start = $prev_it->_getEstimatedFinish( false );
			
			$sql = " SELECT GREATEST(TO_DAYS('".$prev_start."') - TO_DAYS('".
						$this->get_native('StartDate')."'), 0) days, '".$prev_start."' prevStart ";
			
			$it = $this->object->createSQLIterator($sql);
			
			if ( $it->get('days') > 0 )
			{
				if ( $formatted )
				{
					return $it->getDateFormatUser( 'prevStart', $format);
				}
				else
				{
					return $it->get_native('prevStart');
				}
			}
		}

		if ( $formatted )
		{
			return $this->getDateFormatUser( 'StartDate', $format);
		}
		else
		{
			return $this->get_native('StartDate');
		}
	}

	function getFinishOffsetDays()
	{
		list( $left_days, $est_capacity, $est_velocity ) = $this->getEstimatedBurndownMetrics();

		$left_minutes = $left_days * 24 * 60 - 1;
		if ( $left_minutes > 0 )
		{
			$sql = " SELECT GREATEST(TO_DAYS(DATE_ADD(DATE(GREATEST(NOW(),r.StartDate)), INTERVAL ".$left_minutes." MINUTE)) - TO_DAYS(r.FinishDate), 0) days".
				   "   FROM pm_Release r " .
				   "  WHERE r.pm_ReleaseId = ".$this->getId();

			$it = $this->object->createSQLIterator($sql);
			$left_days = $it->get('days');
		}

		return $left_days;
	}
}