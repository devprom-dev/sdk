<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/PMDetailsList.php";

class WorkloadDetailsList extends PMDetailsList
{
	private $strategy = null;

	function setupColumns()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->HasTasks() ) {
			$this->strategy = $methodology_it->TaskEstimationUsed() ? new EstimationHoursStrategy() : new EstimationNoneStrategy();
			$this->buildTasksWorkload($methodology_it);
			if ( $this->strategy instanceof EstimationNoneStrategy ) {
				$this->buildIssuesWorkload($methodology_it, array( new RequestOwnerIsNotTasksAssigneeFilter() ));
			}
		}
		else {
			$this->strategy = $methodology_it->getEstimationStrategy();
			$this->buildIssuesWorkload($methodology_it);
		}

		foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
			if ( $attribute == 'Caption' ) continue;
			$this->getObject()->setAttributeVisible($attribute, false);
		}
		parent::setupColumns();
	}

	function drawCell( $object_it, $attr )
	{
		echo $this->getTable()->getView()->render('pm/UserWorkloadDetails.php', array (
			'user_id' => $object_it->getId(),
			'user_name' => $object_it->getDisplayName(),
			'data' => $this->workload[$object_it->getId()],
			'measure' => trim($this->strategy->getDimensionText(''))
		));
	}

	protected function buildTasksWorkload( $methodology_it )
	{
		$object = getFactory()->getObject('Task');
		$object->setRegistry(new ObjectRegistrySQL($object));
		$object->addFilter( new FilterVpdPredicate() );
		$object->addFilter( new StatePredicate('notterminal') );

		// cache aggregates on workload and spent time
		$planned_aggregate = new AggregateBase(
			'Assignee',
			$this->strategy->getEstimationAggregate() == 'COUNT' ? 'Assignee' : 'Planned',
			$this->strategy->getEstimationAggregate()
		);
		$object->addAggregate( $planned_aggregate );

		$left_aggregate = new AggregateBase(
			'Assignee',
			$this->strategy->getEstimationAggregate() == 'COUNT' ? 'Assignee' : 'LeftWork',
			$this->strategy->getEstimationAggregate()
		);
		$object->addAggregate( $left_aggregate );

		$task_it = $object->getAggregated();
		while( !$task_it->end() )
		{
			$value = $task_it->get($planned_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			$this->workload[$task_it->get('Assignee')]['Planned'] += $value;

			$value = $task_it->get($left_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			$this->workload[$task_it->get('Assignee')]['LeftWork'] += $value;

			$task_it->moveNext();
		}

		if ( $methodology_it->HasPlanning() ) {
			$iteration_registry = getFactory()->getObject('Iteration')->getRegistry();
		}
		else {
			$iteration_registry = getFactory()->getObject('Release')->getRegistry();
		}

		$capacity = array();
		$worker_it = getFactory()->getObject('ProjectUser')->getAll();
		while( !$worker_it->end() ) {
			$capacity[$worker_it->getId()] = $worker_it->get('Capacity');
			$worker_it->moveNext();
		}

		foreach( $this->workload as $user_id => $data )
		{
			if ( $methodology_it->HasPlanning() ) {
				$iteration_it = $iteration_registry->Query(
					array(
						new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
						new IterationUserHasTasksPredicate($user_id),
						new FilterVpdPredicate(),
						new SortAttributeClause('Project'),
						new SortAttributeClause('StartDate')
					)
				);
			}
			else {
				$iteration_it = $iteration_registry->Query(
					array(
						new ReleaseTimelinePredicate('not-passed'),
						new ReleaseUserHasTasksPredicate($user_id),
						new FilterVpdPredicate(),
						new SortAttributeClause('Project'),
						new SortAttributeClause('StartDate')
					)
				);
			}

			$data = array();
			$this->workload[$user_id]['Iterations'] = array();

			if ( $user_id == '' ) continue;

			while( !$iteration_it->end() )
			{
				$data['leftwork'] = $iteration_it->getLeftWorkParticipant( $user_id );
				if ( $data['leftwork'] < 1 ) {
					$iteration_it->moveNext();
					continue;
				}

				$data['title'] = $iteration_it->getHtmlDecoded($methodology_it->HasPlanning() ? 'ShortCaption' : 'Caption');
				$data['capacity'] = $iteration_it->getLeftDuration() * $capacity[$user_id];

				$method = new ObjectModifyWebMethod($iteration_it);
				if ( $method->hasAccess() ) {
					$method->setRedirectUrl('donothing');
					$data['url'] = $method->getJSCall();
				}

				$this->workload[$user_id]['Iterations'][] = $data;
				$iteration_it->moveNext();
			}
		}
	}

	protected function buildIssuesWorkload( $methodology_it, $filters = array() )
	{
		$object = getFactory()->getObject('Request');
		$object->setRegistry(new ObjectRegistrySQL($object));

		$object->addFilter( new FilterVpdPredicate() );
		$object->addFilter( new StatePredicate('notterminal') );
		foreach( $filters as $filter ) {
			$object->addFilter($filter);
		}

		// cache aggregates on workload and spent time
		$planned_aggregate = new AggregateBase(
			'Owner',
			$this->strategy->getEstimationAggregate() == 'COUNT' ? 'Owner' : 'Estimation',
			$this->strategy->getEstimationAggregate()
		);
		$object->addAggregate( $planned_aggregate );
		$left_aggregate = new AggregateBase(
			'Owner',
			$this->strategy->getEstimationAggregate() == 'COUNT' ? 'Owner' : 'EstimationLeft',
			$this->strategy->getEstimationAggregate()
		);
		$object->addAggregate( $left_aggregate );

		$task_it = $object->getAggregated();
		while( !$task_it->end() ) {
			$value = $task_it->get($planned_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			$this->workload[$task_it->get('Owner')]['Planned'] += $value;

			$value = $task_it->get($left_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			$this->workload[$task_it->get('Owner')]['LeftWork'] += $value;

			$task_it->moveNext();
		}

		$iteration_registry = getFactory()->getObject('Release')->getRegistry();
		foreach( $this->workload as $user_id => $data )
		{
			$iteration_it = $iteration_registry->Query(
				array(
					new ReleaseTimelinePredicate('not-passed'),
					new FilterVpdPredicate(),
					new SortAttributeClause('Project'),
					new SortAttributeClause('StartDate')
				)
			);

			$data = array();
			$this->workload[$user_id]['Iterations'] = array();

			if ( $user_id == '' || !$methodology_it->IsAgile() ) continue;

			while( !$iteration_it->end() )
			{
				$request = getFactory()->getObject('pm_ChangeRequest');
				$request->addFilter( new FilterAttributePredicate('PlannedRelease', $iteration_it->getId()) );
				$request->addFilter( new FilterAttributePredicate('Owner', $user_id) );
				$request->addFilter( new StatePredicate('notterminal') );

				$data['leftwork'] = array_shift($this->strategy->getEstimation( $request, 'Estimation' ));
				if ( $data['leftwork'] < 1 ) {
					$iteration_it->moveNext();
					continue;
				}

				$data['title'] = $iteration_it->getHtmlDecoded('Caption');

				list( $capacity, $maximum, $actual_velocity ) = $iteration_it->getRealBurndownMetrics();
				$data['capacity'] = ($capacity / count($this->workload)) * $actual_velocity;

				$method = new ObjectModifyWebMethod($iteration_it);
				if ( $method->hasAccess() ) {
					$method->setRedirectUrl('donothing');
					$data['url'] = $method->getJSCall();
				}

				$this->workload[$user_id]['Iterations'][] = $data;
				$iteration_it->moveNext();
			}
		}
	}
}
