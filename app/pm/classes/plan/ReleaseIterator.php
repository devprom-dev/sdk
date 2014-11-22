<?php

class ReleaseIterator extends OrderedIterator
{
 	function ReleaseIterator( $object ) 
 	{
		parent::OrderedIterator( $object );
	}
	
	function getPrevIt()
	{
		$sql = " SELECT v.* " .
				"  FROM pm_Version v" .
				" WHERE v.Caption < '".$this->get('Caption')."'".
				"   AND v.Project = ".$this->get('Project').
				" ORDER BY v.Caption DESC ".
				" LIMIT 1 ";
				
		return $this->object->createSQLIterator($sql);
	}
	
	function getVelocity()
	{
		return round($this->get('Velocity'), 1);
	}
	
	function getLeftDurationEstimation()
	{
		global $model_factory;
		
		$velocity = $this->getVelocity();
		if ( $velocity <= 0 )
		{
			return 0;
		}
		
		$project_it = $this->getRef('Project');
		$methodology_it = $project_it->getMethodologyIt(); 
		
		if ( $methodology_it->HasFixedRelease() )
		{
			$velocity /= $methodology_it->getReleaseDuration() * 
				$project_it->getDaysInWeek();
		}
		
		return $this->getTotalWorkload() / $velocity;
	}

	function getInitialBurndownMetrics()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$sql =  "SELECT m.PlannedWorkload " .
		 		"  FROM pm_Version r " .
 				"		LEFT OUTER JOIN pm_VersionBurndown m " .
 				"			ON r.pm_VersionId = m.Version " .
		 		" WHERE r.pm_VersionId = ".$this->getId().
		 		" ORDER BY m.SnapshotDays ASC LIMIT 1 ";

		$it = $this->object->createSQLIterator( $sql );
	
		$duration = $this->get('PlannedCapacity') > 0 ? round($this->get('PlannedCapacity'), 0) : $this->getCapacity();
		$capacity = $it->get('PlannedWorkload') > 0 ? $it->get('PlannedWorkload') : $this->getPlannedTotalWorkload();

		if ( !$methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() )
		{
			$velocity = $duration > 0 ? $this->get('InitialVelocity') / $duration : $this->get('InitialVelocity');
		}
		else
		{
			$velocity = $this->getVelocity();
		}
		 	
		return array( $duration, $capacity, $velocity ); 
	}
	
	function getEstimatedBurndownMetrics()
	{
		list( $in_duration, $in_capacity, $in_velocity ) = $this->getInitialBurndownMetrics();
			
		// gets remain capacity of the release
		$duration = $this->getLeftCapacity();
		
		$capacity = $in_velocity > 0 && $duration > 0 ? ceil($duration * $in_velocity) : $this->getTotalWorkload();
		
		if ( $capacity < 1 ) $capacity = $in_velocity;
		
		return array( $duration, $capacity, $in_velocity );
	}
	
	function _getEstimatedStart( $formatted = false, $format = '' )
	{
		if ( $format == '' )
		{
			$format = getSession()->getLanguage()->getDateFormat();
		}
		
		if ( $this->get_native('StartDate') != '' )
		{
			$request_sql = "'".$this->get_native('StartDate')."'"; 
		}
		else
		{
			$request_sql = " (SELECT MIN(r.RecordCreated) FROM pm_ChangeRequest r " .
				" WHERE r.PlannedRelease = ".$this->getId()." AND r.State = 'resolved') ";
		}
		
		$sql = " SELECT IFNULL( (SELECT MIN(r.StartDate) FROM pm_Release r WHERE r.Version = ".
			$this->getId()."), ".$request_sql." ) StartDate ";
			   
		$it = $this->object->createSQLIterator( $sql );

		$start_date = $it->get('StartDate');
		
		if ( $formatted )
		{
			return getSession()->getLanguage()->getDateUserFormatted($start_date, $format);
		}
		else
		{
			return $start_date;
		}
	}
	
	function _getEstimatedFinish( $formatted = true, $format = '' )
	{
		global $project_it;
		
		if ( $format == '' )
		{
			$format = getSession()->getLanguage()->getDateFormat();
		}

		list( $duration, $est_capacity, $est_velocity ) = $this->getEstimatedBurndownMetrics();
		
		if ( $duration == '' )
		{
			$duration = 0;
		}

		if ( $duration > 0 || !$this->IsCompleted() )
		{
			$finish_date = "GREATEST(FROM_DAYS(TO_DAYS(NOW()) + ".$duration." - 1), '".$this->get_native('FinishDate')."')";
		}
		else
		{
			$finish_date = " IFNULL((SELECT MAX(r.RecordModified) FROM pm_ChangeRequest r " .
				" WHERE r.PlannedRelease = ".$this->getId()."), '".$this->get_native('FinishDate')."') ";
		}

		$it = $this->object->createSQLIterator(" SELECT (".$finish_date.") EstDate");
		$finish_date = $it->get('EstDate');
		
		if ( $formatted )
		{
			return getSession()->getLanguage()->getDateUserFormatted( $finish_date, $format );
		}
		else
		{
			return $finish_date;
		}
	}
	
	function getFinishOffsetDays()
	{
		$est_finish = $this->get('EstimatedFinishDate');
		
		$sql = " SELECT TO_DAYS('".$est_finish."') - TO_DAYS('".$this->get_native('FinishDate')."') diff," .
			   "        TO_DAYS('".$est_finish."') - TO_DAYS(NOW()) obsolete ";
		
		$it = $this->object->createSQLIterator($sql);
			
		if ( $it->get('obsolete') < 0 )
		{
			return 0;
		}
		else
		{
			return $it->get('diff');
		}
	}

	function getInterval( $interval )
	{
		if ( !is_object($this->object->interval_it) )
		{
			$this->object->cacheDeps();
		}
		
		$this->object->interval_it->moveTo('pm_VersionId', $this->getId());
		return $this->object->interval_it->get($interval);
	}
	
	function hasMetrics()
	{
		$sql = " SELECT COUNT(1) cnt " .
		 	   "   FROM pm_VersionMetric m " .
		 	   "  WHERE m.Version = " .$this->getId();

		$it = $this->createSQLIterator($sql);
		
		return $it->get('cnt') > 0;		
	}
	
	function storeMetrics()
	{
		global $model_factory;
		
		$project_it = getSession()->getProjectIt();
		$methodology_it = $project_it->getMethodologyIt();
		
		// calculate metrics for each iteration
		if ( $methodology_it->HasPlanning() )
		{
			$iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('Version', $this->getId()),
							new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED)
					)
			);
	
			while ( !$iteration_it->end() )
			{
				$iteration_it->storeMetrics();
				$iteration_it->moveNext();
			}
		}		

		// calculate metrics for release
		$estimation = $this->getPlannedTotalWorkload();
		$total_workload = $this->getTotalWorkload();
		
		// calculate release velocity
		
		$velocity = 0;
		
		if ( $methodology_it->HasPlanning() )
		{
			$iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
					array (
							new IterationTimelinePredicate(IterationTimelinePredicate::PAST),
							new IterationReleasePredicate($this->getId())
					)
			);
				
			$sql = " SELECT IFNULL(AVG(m.MetricValue), 0) Velocity " .
				   "   FROM pm_IterationMetric m, pm_Release r " .
				   "  WHERE r.pm_ReleaseId = m.Iteration" .
				   "    AND m.Metric = 'Velocity' ".
			 	   "    AND m.MetricValue > 0" .
			 	   "    AND r.pm_ReleaseId IN (".join($iteration_it->idsToArray(), ',').") ".
			 	   "  ORDER BY r.StartDate DESC LIMIT 3 ";

			$velit = $this->object->createSQLIterator($sql);
			
			$velocity = round($velit->get('Velocity'), 0);
			if ( $velocity <= 0 ) $velocity = $this->get('InitialVelocity');
			
			if ( $methodology_it->HasFixedRelease() )
			{
				$velocity /= $methodology_it->getReleaseDuration() * $project_it->getDaysInWeek();
			}
		}
		else
		{ 
			$completed = $this->getCompletedWorkload();
			if ( $methodology_it->HasFixedRelease() )
			{
				$velocity = $completed;
			}
			else
			{
				$capacity = $this->getSpentCapacity();
				if ( $capacity > 0 )
				{
					$velocity = round($completed / $capacity, 1);
				}
			}
			if ( $velocity <= 0 ) $velocity = $this->get('InitialVelocity');
		}
		
		// calculate estimated start and finish dates
		//
		$estimation = max($estimation, $this->getCapacity() * $velocity); 
		
		$metrics = array (
			'Workload' => $total_workload,
			'Estimation' => $estimation,
			'Velocity' => $velocity 
		);
		
		$date_metrics = array (
			'EstimatedStart' => $this->_getEstimatedStart(false),
			'EstimatedFinish' => $this->_getEstimatedFinish(false)
		);
		
		$metric = $model_factory->getObject('pm_VersionMetric');
		$metric->setNotificationEnabled(false);
		
		DAL::Instance()->Query("LOCK TABLES pm_VersionMetric t WRITE, pm_VersionMetric WRITE");

		$sql = "DELETE FROM pm_VersionMetric WHERE Version = ".$this->getId();
		
		DAL::Instance()->Query($sql);
		
		foreach ( $metrics as $key => $value )
		{
			$metric->add_parms(
				array('Version' => $this->getId(),
					  'Metric' => $key,
					  'MetricValue' => $value) );
		}

		foreach ( $date_metrics as $key => $value )
		{
			$metric->add_parms(
				array('Version' => $this->getId(),
					  'Metric' => $key,
					  'MetricValueDate' => $value) );
		}
		
		DAL::Instance()->Query("UNLOCK TABLES");
	}
	
	function getMetricsDate()
	{
		$sql = " SELECT MAX(m.RecordModified) LastDate " .
			   "   FROM pm_VersionMetric m, pm_Version v " .
			   "  WHERE m.Version = ".$this->getId();
			   
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->getDateTimeFormat('LastDate');
	}

	function storeBurndownSnapshot()
	{
		$project_it = getSession()->getProjectIt();
		
		$sql = " SELECT GREATEST(LEAST(TO_DAYS(NOW()), TO_DAYS(r.FinishDate)), TO_DAYS(r.StartDate)) BeginDays, ".
			   "	    r.FinishDate " .
			   "   FROM pm_Version r " .
			   "  WHERE r.pm_VersionId = ".$this->getId();
			   
		$it = $this->object->createSQLIterator($sql);
			   
		$metrics = getFactory()->getObject('pm_VersionBurndown');
			
		$metrics_it = $metrics->getByRefArray(
			array( 'Version' => $this->getId(),
				   'SnapshotDays' => $it->get("BeginDays")) 
			);

		list( $duration, $workload, $velocity ) = $this->getEstimatedBurndownMetrics();
		
		$workload = $this->getTotalWorkload();
		$plannedworkload = max($this->getPlannedTotalWorkload(), $duration * $velocity);
		
		if ( $this->IsFinished() )
		{
			$metric_date = $it->get('FinishDate');
		}
		else
		{
			$metric_date = 'NOW()';
		}
		
		if ( $metrics_it->count() == 1 && $plannedworkload == 0 )
		{
			$plannedworkload = $metrics_it->get('PlannedWorkload');
		}

		while ( !$metrics_it->end() )
		{
			$metrics_it->delete();
			$metrics_it->moveNext();
		}

		$metrics->add_parms( 
			array( 'Version' => $this->getId(),
				   'SnapshotDays' => $it->get("BeginDays"),
				   'SnapshotDate' => $metric_date,
				   'Workload' => $workload,
				   'PlannedWorkload' => $plannedworkload ) );
	}
	
	function resetBurndown()
	{
		global $model_factory;
		
		$sql = 
			" DELETE FROM pm_VersionBurndown " .
			"  WHERE Version = ".$this->getId().
			"    AND DATE(SnapshotDate) NOT BETWEEN '".$this->get_native('StartDate')."'".
			"    AND '".$this->get_native('FinishDate')."'";

		DAL::Instance()->Query($sql);			
		
		$sql = 
			" DELETE FROM pm_VersionBurndown " .
			"  WHERE Version = ".$this->getId().
			"    AND DATE(SnapshotDate) > DATE(NOW())";

		DAL::Instance()->Query($sql);			
		
		$sql = " SELECT r.* " .
			   "   FROM pm_VersionBurndown r " .
			   "  WHERE r.Version = ".$this->getId().
			   "    AND r.SnapshotDays = (SELECT MIN(r2.SnapshotDays) FROM pm_VersionBurndown r2 " .
			   "						   WHERE r2.Version = ".$this->getId().") ";
			   
		$metrics = $model_factory->getObject('pm_VersionBurndown');
		$it = $metrics->createSQLIterator($sql);
		
		while ( !$it->end() )
		{
			$metrics->modify_parms( $it->getId(), array ( 
				'PlannedWorkload' => $this->getPlannedTotalWorkload() 
			));
			$it->moveNext();
		}
		
		$this->storeMetrics();
	}

	function getStartDate()
	{
		return $this->getDateFormat('StartDate');
	}
	
	function getFinishDate()
	{
		return $this->getDateFormat('FinishDate');
	}
	
	function getIterationIt()
	{
		global $model_factory;
		$ids = $this->idsToArray();
		
		$iteration = $model_factory->getObject('pm_Release');
		$iteration->defaultsort = 'StartDate ASC';
		
		return $iteration->getInArray('Version', $ids);
	}
	
	function getRecentBuildIt()
	{
		global $model_factory;
		
		$sql = 
			" SELECT b.* " .
			"   FROM pm_Build b" .
			"  WHERE b.Version = ".$this->getId().
			"  ORDER BY b.Caption DESC" .
			"  LIMIT 1";
			
		$build = $model_factory->getObject('pm_Build');
		return $build->createSQLIterator( $sql );
	}
	
	function getRecentIterationIt()
	{
		global $model_factory;
		
		$iteration = $model_factory->getObject('pm_Release');
		$iteration->defaultsort = 'FinishDate DESC';
		
		return $iteration->getByRefArray(
			array( 'Version' => $this->getId()), 1);
	}

	function getNextIterationStart()
	{
		global $model_factory;
		
		$iteration = $model_factory->getObject('Iteration');
		
		$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
		
		$iteration->addFilter( new IterationReleasePredicate($this->getId()) );
		
		$iteration->addSort( new SortAttributeClause('StartDate.D') );
		
		$iteration_it = $iteration->getAll();

		if ( $iteration_it->count() < 1 )
		{
			return $this->getDateFormat('EstimatedStartDate');
		}
		else
		{
			return date( 'Y-m-j', strtotime('1 day', strtotime( $iteration_it->get('FinishDate') ) ) );
		}
	}
	
	function IsCurrent()
	{
		if ( !$this->IsFuture() )
		{
			return false;
		}

		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get('EstimatedFinishDate')."') diff ";
		$it = $this->object->createSQLIterator( $sql );

		return $it->get('diff') <= 0;
	}
	
	function IsFuture()
	{
		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get_native('EstimatedStartDate')."') diff ";
			   
		$it = $this->object->createSQLIterator( $sql );
		return $it->get('diff') < 0;
	}
	
	function IsFinished() 
	{
		$finish = $this->get('EstimatedFinishDate');
		
		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$finish."') Offset ";
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Offset') > 0;
	}

	function IsCompleted()
	{
		global $model_factory;
		
		$project_it = $this->getRef('Project');
		if ( !$project_it->IsActive() )
		{
			return true;
		}
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->addFilter( new StatePredicate('notresolved') );
		$request->addFilter( new FilterAttributePredicate('PlannedRelease', $this->getId()) );

		return $request->getRecordCount() < 1;
	}
	
	function IsEmpty()
	{
		global $model_factory;
		$iteration = $model_factory->getObject('Iteration');
		
		$result = $iteration->getByRefArrayCount(
			array('Version' => $this->getId() ) ) < 1;
			
		$build = $model_factory->getObject('pm_Build');
		
		$result &= $build->getByRefArrayCount(
			array('Version' => $this->getId() ) ) < 1;

		return $result;
	}

	function getProgress()
	{
		global $model_factory;
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		$request->addFilter( new FilterAttributePredicate('PlannedRelease', $this->getId()) );
		
		$request_it = $request->getAll();
		
		$task = $model_factory->getObject('pm_Task');
		$types_map = $task->getTypesMap();
		
		$progress = $request_it->getProgress();
		
 		$sql = " SELECT COUNT(1) Total, SUM(CASE t.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved " .
 			   "   FROM pm_Task t, pm_Release r " .
 			   "  WHERE t.ChangeRequest IS NULL ".
 			   "    AND t.Release = r.pm_ReleaseId ".
 			   "    AND r.Version = ".$this->getId();
 		
 		$it = $this->object->createSQLIterator( $sql );
		
 		while ( !$it->end() )
 		{
 			$progress['D'][0] += $it->get('Total');
 			$progress['D'][1] += $it->get('Resolved');

 			$it->moveNext();
 		}

		return $progress;
	}

	function getPlannedTotalWorkload() 
	{
		$request = getFactory()->getObject('pm_ChangeRequest');

		$request->addFilter( new FilterAttributePredicate('PlannedRelease', $this->getId()) );
		
		return array_shift(getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getEstimation( $request )); 
	}

	function getTotalWorkload() 
	{
		$request = getFactory()->getObject('pm_ChangeRequest');
		
		$request->addFilter( new FilterAttributePredicate('PlannedRelease', $this->getId()) );
		$request->addFilter( new StatePredicate('notterminal') );
		
		return array_shift(
				getSession()->getProjectIt()->getMethodologyIt()
					->getEstimationStrategy()->getEstimation( $request, 'Estimation')
		); 
	}

	function getCompletedWorkload() 
	{
		global $model_factory;
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->addFilter( new FilterAttributePredicate('PlannedRelease', $this->getId()) );
		$request->addFilter( new StatePredicate('terminal') );
		
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		
		$data = $strategy->getEstimation( $request );
		
		return array_shift($data); 
	}
	
	function getCapacity()
	{
	    return round($this->get('PlannedCapacity'), 0);
	}

	function getLeftCapacity()
	{
	    return round($this->get('LeftCapacityInWorkingDays'), 0);
	}
	
	function getSpentCapacity()
	{
	    return round($this->get('ActualDurationInWorkingDays'), 0);
	}

	function getLeftWorkParticipant( $part_it )
	{
		global $model_factory;
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		$request->addFilter( new StatePredicate('notresolved') );
		
		$sql = " SELECT SUM(IFNULL(a.LeftWork, 0)) Workload " .
			   "   FROM pm_ChangeRequest t, pm_Task a " .
			   "  WHERE t.PlannedRelease = ".$this->getId().
			   "	AND a.Assignee = ".$part_it->getId().
			   "    AND a.ChangeRequest = t.pm_ChangeRequestId ".
			   $request->getFilterPredicate();
		
		$it = $request->createSQLIterator( $sql );
		
		return $it->get('Workload');
	}
}
