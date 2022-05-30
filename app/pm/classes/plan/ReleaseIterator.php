<?php
include_once "IterationDatesIterator.php";

class ReleaseIterator extends IterationDatesIterator
{
	function getVelocity()
	{
		return round($this->get('Velocity'), 1);
	}
	
	function getInitialBurndownMetrics()
	{
		$methodology_it = $this->getRef('Project')->getMethodologyIt();
		
		$duration = $this->getDuration();
		$capacity = $this->get('PlannedWorkload') > 0 ? $this->get('PlannedWorkload') : $this->getPlannedTotalWorkload();
        $velocity = $this->get('InitialVelocity');

		if ( !$methodology_it->HasPlanning() && $methodology_it->HasFixedRelease() ) {
			$velocity = $duration > 0 ? $velocity / $duration : $velocity;
		}

		return array( $duration, $capacity, $velocity ); 
	}
	
	function getRealBurndownMetrics()
	{
		// get remain duration of the release
		$duration = $this->getLeftDuration();
        // get initial velocity
        list( $in_duration, $in_capacity, $velocity ) = $this->getInitialBurndownMetrics();
		
		$capacity = $velocity > 0 && $duration > 0 ? ceil($duration * $velocity) : 0;
		if ( $capacity < 1 ) $capacity = $velocity;
		
		return array( $duration, $capacity, $velocity, $this->getTotalWorkload() );
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
				" WHERE r.PlannedRelease = ".$this->getId()." AND r.FinishDate IS NOT NULL) ";
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

    function getWorkItemsMaxDateQuery() {
        return " (SELECT IFNULL(MAX(r.FinishDate), '".$this->get_native('FinishDate')."') 
                    FROM pm_ChangeRequest r WHERE r.PlannedRelease = ".$this->getId().") ";
    }

	function getFinishOffsetDays()
	{
		$est_finish = $this->get('EstimatedFinishDate');

		$it = $this->object->createSQLIterator(
            " SELECT TO_DAYS('".$est_finish."') - TO_DAYS('".$this->get_native('FinishDate')."') diff "
        );
		return $it->get('diff');
	}

	function storeMetrics()
	{
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
			while ( !$iteration_it->end() ) {
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
			if ( $methodology_it->HasFixedRelease() ) {
				$velocity = $completed;
			}
			else
			{
				$capacity = $this->getSpentDuration();
				if ( $capacity > 0 ) {
					$velocity = round($completed / $capacity, 1);
				}
			}
			if ( $velocity <= 0 ) $velocity = $this->get('InitialVelocity');
		}
		
		// calculate estimated start and finish dates
		//
		$estimation = max($estimation, $this->getDuration() * $velocity);
		
		$metrics = array (
			'Workload' => $total_workload,
			'Estimation' => $estimation,
			'Velocity' => $velocity 
		);
		
		$date_metrics = array (
			'EstimatedStart' => $this->_getEstimatedStart(false),
			'EstimatedFinish' => EstimationProxy::getEstimatedFinish($this, false)
		);
		
		$metric = getFactory()->getObject('pm_VersionMetric');
		$metric->setNotificationEnabled(false);

        DAL::Instance()->Query("DELETE FROM pm_VersionMetric WHERE Version = " . $this->getId());

        foreach ($metrics as $key => $value) {
            $metric->add_parms(array(
                'Version' => $this->getId(),
                'Metric' => $key,
                'MetricValue' => $value
            ));
        }

        foreach ($date_metrics as $key => $value) {
            $metric->add_parms(array(
                'Version' => $this->getId(),
                'Metric' => $key,
                'MetricValueDate' => $value
            ));
        }
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

		list( $duration, $capacity, $velocity, $workload ) = $this->getRealBurndownMetrics();
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

	function IsCurrent()
	{
        return $this->object->getRegistry()->Count(
                array(
                    new ReleaseTimelinePredicate('current'),
                    new FilterInPredicate($this->getId())
                )
            ) > 0;
	}
	
	function IsFuture()
	{
		$sql = " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get_native('EstimatedStartDate')."') diff ";
			   
		$it = $this->object->createSQLIterator( $sql );
		return $it->get('diff') < 0;
	}
	
	function IsFinished() 
	{
		return $this->object->createSQLIterator(
		        " SELECT TO_DAYS(NOW()) - TO_DAYS('".$this->get('EstimatedFinishDate')."') `Offset` FROM dual"
            )->get('Offset') > 0;
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
	
	function getDuration()
	{
	    return round($this->get('PlannedDurationInWorkingDays'), 0);
	}

	function getLeftDuration()
	{
	    return round($this->get('LeftDurationInWorkingDays'), 0);
	}
	
	function getSpentDuration()
	{
	    return round($this->get('ActualDurationInWorkingDays'), 0);
	}

	function getLeftWorkParticipant( $userId )
	{
		if ( $userId < 1 ) return 0;
		if ( $this->getId() < 1 ) return 0;

		$sql = "SELECT SUM(IFNULL(t.LeftWork, 0)) leftwork ".
			"  FROM pm_Task t, pm_ChangeRequest r ".
			" WHERE t.FinishDate IS NULL ".
			"   AND t.Assignee = ".$userId.
			"   AND r.pm_ChangeRequestId = t.ChangeRequest ".
			"	AND r.PlannedRelease = " .$this->getId().
            " UNION ".
            "SELECT SUM(IFNULL(r.EstimationLeft, 0)) leftwork ".
            "  FROM pm_ChangeRequest r ".
            " WHERE r.FinishDate IS NULL ".
            "   AND r.Owner = ".$userId.
            "	AND r.PlannedRelease = " .$this->getId().
            "   AND NOT EXISTS (SELECT 1 FROM pm_Task t WHERE t.ChangeRequest = r.pm_ChangeRequestId)";

		$it = $this->object->createSQLIterator( $sql );
		return $it->get('leftwork');
	}

    function getCommentsUrl() {
 	    return '';
    }
}
