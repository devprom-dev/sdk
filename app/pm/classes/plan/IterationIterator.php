<?php
include_once "EstimationProxy.php";
include_once "IterationDatesIterator.php";

class IterationIterator extends IterationDatesIterator
{
	function IsFinished() 
	{
		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get('EstimatedFinishDate')."') Offset ";
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->get('Offset') > 0;
	}
	
	function IsCurrent()
	{
	    return $this->object->getRegistry()->Count(
	        array(
	            new IterationTimelinePredicate(IterationTimelinePredicate::CURRENT),
                new FilterInPredicate($this->getId())
            )
        ) > 0;
	}

	function IsFuture()
	{
		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get_native('StartDate')."') diff ";

		$it = $this->object->createSQLIterator( $sql );
		return $it->get('diff') < 0;
	}
	
	function getDuration()
	{
	    return $this->get('Capacity');
	}

	function getSpentDuration()
	{
	    return $this->get('ActualDurationInWorkingDays');
	}

	function getPlannedCapacity() 
	{
	    return $this->get('PlannedCapacity');
	}
	
	function getLeftDuration()
	{
	    return $this->get('LeftCapacityInWorkingDays');
	}

	function getTotalWorkload( $predicates = array() ) 
	{
		$task = getFactory()->getObject('pm_Task');
		$task->setVpdContext($this);

		$task->addFilter( new FilterAttributePredicate('Release', $this->getId()) );
		$task->addFilter( new StatePredicate('notresolved') );
		
		foreach( $predicates as $predicate ) $task->addFilter( $predicate );
		
		$methodology_it = $this->getRef('Project')->getMethodologyIt();
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
		$request = getFactory()->getObject('pm_ChangeRequest');
		$request->addFilter( new RequestIterationFilter($this->getId()) );
				
		return array_shift(
            $this->getRef('Project')->getMethodologyIt()->getIterationEstimationStrategy()->getEstimation( $request )
		);
	}

	function getCompletedEstimation()
	{
		$request = getFactory()->getObject('pm_ChangeRequest');
		$request->addFilter( new RequestIterationFilter($this->getId()) );
		$request->addFilter( new StatePredicate('terminal') );
		return array_shift(
            $this->getRef('Project')->getMethodologyIt()->getIterationEstimationStrategy()->getEstimation( $request )
		);
	}

	function getLeftEstimation()
	{
		$request = getFactory()->getObject('pm_ChangeRequest');
		$request->addFilter( new RequestIterationFilter($this->getId()) );
		$request->addFilter( new StatePredicate('notresolved') );

		return array_shift(
			$this->getRef('Project')->getMethodologyIt()->getIterationEstimationStrategy()->getEstimation( $request )
		);
	}

	function getInitialBurndownMetrics()
	{
		$methodology_it = $this->getRef('Project')->getMethodologyIt();

		$duration = $this->getPlannedCapacity();
		$capacity = $this->get('PlannedWorkload') > 0 ? $this->get('PlannedWorkload') : $this->getPlannedTotalWorkload();
        $velocity = $this->get('InitialVelocity');

        if ( $methodology_it->HasFixedRelease() && $duration > 0 ) {
            $velocity = $velocity / $duration;
        }

		return array( $duration, $capacity, $velocity );
	}

	protected function getEstimationInitialBurndownMetrics()
	{
		$methodology_it = $this->getRef('Project')->getMethodologyIt();

		$duration = $this->get('PlannedCapacity') > 0 ? round($this->get('PlannedCapacity'), 0) : $this->getDuration();
		$capacity = $this->get('PlannedEstimation') > 0 ? $this->get('PlannedEstimation') : $this->getEstimation();
        $velocity = $this->get('InitialVelocity');

		if ( $methodology_it->HasFixedRelease() && $duration > 0 ) {
			$velocity = $velocity / $duration;
		}
		return array( $duration, $capacity, $velocity );
	}

	function getRealBurndownMetrics()
	{
        if ( $this->getRef('Project')->getMethodologyIt()->RequestEstimationUsed() ) {
            return $this->getEstimationRealBurndownMetrics();
        }
        else {
            $duration = $this->getLeftDuration();
            // get initial velocity
            list( $in_duration, $in_capacity, $velocity ) = $this->getInitialBurndownMetrics();
            $capacity = $velocity > 0 ? ceil($duration * $velocity) : 0;

            return array( $duration, $capacity, $velocity, $this->getTotalWorkload() );
        }
	}

	protected function getEstimationRealBurndownMetrics()
	{
		$duration = $this->getLeftDuration();
        // get initial velocity
        list( $in_duration, $in_capacity, $velocity ) = $this->getEstimationInitialBurndownMetrics();
		$capacity = $velocity > 0 ? ceil($duration * $velocity) : 0;

		return array( $duration, $capacity, $velocity, $this->getLeftEstimation() );
	}

	function getLeftWorkParticipant( $userId )
	{
		if ( $userId < 1 ) return 0;
		if ( $this->getId() < 1 ) return 0;
		
		$sql = "SELECT SUM(IFNULL(t.LeftWork, 0)) leftwork ".
			   "  FROM pm_Task t ".
			   " WHERE t.FinishDate IS NULL ".
			   "   AND t.Assignee = ".$userId.
  		 	   "   AND t.Release = " .$this->getId();

		$it = $this->object->createSQLIterator( $sql );			   
		return $it->get('leftwork');
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
	
	function storeMetricsSnapshot()
	{
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
		$plannedEstimation = $this->getEstimation();
		$estimation = $this->getLeftEstimation();
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
				? $plannedworkload : $it->get('PlannedWorkload'),
			'PlannedEstimation' => $plannedEstimation,
			'Estimation' => $estimation
		));
	}

	function resetBurndown()
	{
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
			   
		$metrics = getFactory()->getObject('pm_ReleaseMetrics');
		$it = $metrics->createSQLIterator($sql);
		while ( !$it->end() )
		{
			$metrics->modify_parms( $it->getId(), array (
				'PlannedWorkload' => $this->getPlannedTotalWorkload(array()),
				'PlannedEstimation' => $this->getEstimation()
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
		if ( $this->getRef('Project')->getMethodologyIt()->HasFixedRelease() ) {
			$velocity = round($estimation, 1);
		}
		else {
		    $capacity = $this->getSpentDuration();
			$velocity = $capacity > 0 ? round($estimation / $capacity, 1) : 0;
		}

		if ( $velocity <= 0 ) {
			$velocity = $this->get('InitialVelocity');
			if ( $velocity <= 0 ) {
				$velocity = $release_it->get('InitialVelocity');
			}
		}

		$metrics = array (
			'Velocity' => $velocity
		);
		$date_metrics = array (
			'EstimatedStart' => $this->_getEstimatedStart(false),
			'EstimatedFinish' => EstimationProxy::getEstimatedFinish($this, false)
		);
		 
		if ( $this->IsCurrent() )
		{
			$metrics['ReleaseEstimation'] = $release_it->getPlannedTotalWorkload();
		}
		
		$metrics['IterationEstimation'] = $estimation;
		
		$metric = getFactory()->getObject('pm_IterationMetric');
		$metric->setNotificationEnabled(false);

        DAL::Instance()->Query("DELETE FROM pm_IterationMetric WHERE Iteration = ".$this->getId());

        foreach ( $metrics as $key => $value ) {
            $metric->add_parms(
                array( 'Iteration' => $this->getId(),
                       'Metric' => $key,
                       'MetricValue' => $value ) );
        }

        foreach ( $date_metrics as $key => $value ) {
            $metric->add_parms(
                array( 'Iteration' => $this->getId(),
                       'Metric' => $key,
                       'MetricValueDate' => $value ) );
        }
	}
	
	function getSeparateTaskIt()
	{
		$sql = 'SELECT * FROM pm_Task t ' .
			   ' WHERE t.Release = ' .$this->getId().
			   '   AND t.ChangeRequest IS NULL '.
			   ' ORDER BY t.Priority, t.OrderNum, t.State, t.TaskType, t.RecordCreated ASC';

		return getFactory()->getObject('pm_Task')->createSQLIterator($sql);
	}
 
	function getStartDate()
	{
		return $this->getDateFormatted('StartDate');
	}
	
	function getFinishDate()
	{
		return $this->getDateFormatted('FinishDate');
	}

	function getWorkItemsMaxDateQuery() {
        return " GREATEST(
                    (SELECT IFNULL(MAX(r.FinishDate), '".$this->get_native('FinishDate')."') 
                        FROM pm_ChangeRequest r WHERE r.Iteration = ".$this->getId()."),
                    (SELECT IFNULL(MAX(r.FinishDate), '".$this->get_native('FinishDate')."') 
                        FROM pm_Task r WHERE r.Release = ".$this->getId().")
                  ) ";
    }

	function _getEstimatedStart( $formatted = true, $format = '')
	{
		if ( $format == '' ) $format = getSession()->getLanguage()->getDateFormat();

		$prev_it = $this->getPrevSiblingRelease();

		if ( $prev_it->count() > 0 )
		{
			$prev_start = EstimationProxy::getEstimatedFinish($this, false);
			
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
        $est_finish = $this->get('EstimatedFinishDate');

        $it = $this->object->createSQLIterator(
            " SELECT TO_DAYS('".$est_finish."') - TO_DAYS('".$this->get_native('FinishDate')."') diff  "
        );
        return $it->get('diff');
	}

    function getCommentsUrl() {
        return '';
    }
}