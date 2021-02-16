<?php
namespace Devprom\ProjectBundle\Service\Project;

include_once SERVER_ROOT_PATH . "pm/classes/project/MetricIssueBuilder.php";
include_once SERVER_ROOT_PATH . "pm/classes/product/persisters/FeatureMetricsPersister.php";
include_once SERVER_ROOT_PATH . "pm/classes/issues/persisters/RequestMetricsPersister.php";

class StoreMetricsService
{
 	function execute( $project_it, $force = false )
 	{
 		$this->storeProjectMetrics(
 		    $project_it,
            getFactory()->getObject('Release')->getRegistry()->Query(
                array (
                    new \FilterAttributePredicate('Project', $project_it->getId()),
                    $force
                        ? new \FilterDummyPredicate()
                        : new \ReleaseTimelinePredicate('not-passed')
                )
            ),
            getFactory()->getObject('Iteration')->getRegistry()->Query(
                array (
                    new \FilterAttributePredicate('Project', $project_it->getId()),
                    new \FilterAttributeNullPredicate('Version'),
                    $force
                        ? new \FilterDummyPredicate()
                        : new \IterationTimelinePredicate(\IterationTimelinePredicate::NOTPASSED)
                )
            )
        );

		$registry = getFactory()->getObject('Request')->getRegistry();
 		$this->storeIssueMetrics(
			$registry,
			array (
				new \FilterVpdPredicate($project_it->get('VPD')),
				new \StatePredicate('terminal'),
				new \FilterAttributePredicate('DeliveryDate', 'none'),
				new \RequestMetricsPersister()
			)
		);
		$this->storeIssueMetrics(
			$registry,
			array (
				new \FilterVpdPredicate($project_it->get('VPD')),
				new \StatePredicate('notresolved'),
				new \RequestMetricsPersister()
			)
		);
 		$this->storeIssueMetrics(
			$registry,
			array (
				new \FilterVpdPredicate($project_it->get('VPD')),
				new \StatePredicate('notresolved'),
				new \RequestDependencyFilter('duplicates,implemented,blocked'),
				new \RequestMetricsPersister()
			)
		);

		$registry = getFactory()->getObject('Feature')->getRegistry();
		$this->storeFeatureMetrics(
			$registry,
			array (
				new \FilterVpdPredicate($project_it->get('VPD')),
				new \FeatureMetricsPersister()
			)
		);
 	}
 	
 	function storeProjectMetrics( $project_it, $version_it, $iteration_it )
 	{
        getFactory()->resetCachedIterator($project_it->object);

        if ( $version_it instanceof \OrderedIterator ) {
            while ( !$version_it->end() ) {
                $version_it->storeMetrics();
                $version_it->moveNext();
            }
        }

        if ( $iteration_it instanceof \OrderedIterator ) {
            while ( !$iteration_it->end() ) {
                $iteration_it->storeMetrics();
                $iteration_it->moveNext();
            }
        }

        $methodology_it = $project_it->getMethodologyIt();
        $finishDate = '';

        if ( $methodology_it->HasReleases() || $methodology_it->HasPlanning() ) {
            $velocity = $project_it->getVelocityDevider();
        }
        else {
            $issue = getFactory()->getObject('Request');

            $registry = $issue->getRegistry();
            $registry->setLimit(10);
            $solvedIt = $registry->Query(
                array (
                    new \FilterVpdPredicate($project_it->get('VPD')),
                    new \StatePredicate('terminal')
                )
            );

            $aggregateFunc = new \AggregateBase( 'VPD', 'LifecycleDuration', 'AVG' );
            $issue->addFilter( new \FilterInPredicate($solvedIt->idsToArray()) );
            $issue->addAggregate($aggregateFunc);
            $velocity = $issue->getAggregated('t')->get($aggregateFunc->getAggregateAlias());

            $leftRequests = $issue->getRegistry()->Count(
                array (
                    new \FilterVpdPredicate($project_it->get('VPD')),
                    new \StatePredicate('notterminal')
                )
            );
            $leftDays = $leftRequests * $velocity;
            $finishDate = strftime('%Y-%m-%d', strtotime(round($leftDays,0).' days', strtotime(date('Y-m-d'))));
        }

 		if ( $finishDate == '' )
 		{
            $stage = getFactory()->getObject('Stage');
            $stage_aggregate = new \AggregateBase( 'Project', 'EstimatedFinishDate', 'MAX' );
            $stage->addAggregate($stage_aggregate);
            $finishDate = $stage->getAggregated()->get($stage_aggregate->getAggregateAlias());
 		}

		$project_it->object->setNotificationEnabled(false);
 		$project_it->object->modify_parms($project_it->getId(), 
            array (
                'Rating' => $velocity,
                'FinishDate' => $finishDate,
                'RecordModified' => $project_it->get('RecordModified')
            )
		);

 		$metricIt = getFactory()->getObject('Metric')->getAll();
        $metrics_registry = getFactory()->getObject('ProjectMetric')->getRegistry();
        foreach( $project_it->object->getAttributesByGroup('metrics') as $attribute ) {
            $metricIt->moveToId($attribute);
            $metrics_registry->setMetric($attribute, $project_it->get($attribute), $metricIt->getDisplayName());
        }
 	}
 	
 	function storeFeatureMetrics( $registry, $queryParms )
 	{
		$registry->setPersisters(array());
		$feature_it = $registry->Query($queryParms);

        getFactory()->resetCachedIterator($feature_it->object);
        $feature_it->object->setNotificationEnabled(false);

 		while( !$feature_it->end() )
 		{
			$registry->Store(
	 				$feature_it,
	 				array (
						'Estimation' => $feature_it->get('MetricEstimation'),
						'EstimationLeft' => $feature_it->get('MetricEstimationLeft'),
						'Workload' => $feature_it->get('MetricWorkload'),
						'StartDate' => $feature_it->get('MetricStartDate'),
						'DeliveryDate' => $feature_it->get('MetricDeliveryDate'),
						'RecordModified' => $feature_it->get('RecordModified')
	 				)
	 		);
 			$feature_it->moveNext();
 		}
 	}
 	
 	function storeIssueMetrics( $registry, $queryParms  )
 	{
		$issue_it = $registry->Query($queryParms);

        getFactory()->resetCachedIterator($issue_it->object);
        $issue_it->object->setNotificationEnabled(false);

        $registry->getObject()->setNotificationEnabled(false);
        $customBuilders = getSession()->getBuilders('MetricIssueBuilder');

 		while( !$issue_it->end() )
 		{
			$parms = array();
 			if ( $issue_it->get('MetricDeliveryDate') != $issue_it->get('DeliveryDate') ) {
                $parms['DeliveryDate'] = $issue_it->get('MetricDeliveryDate');
                $parms['DeliveryDateMethod'] = $issue_it->get('MetricDeliveryDateMethod');
            }

            list($total, $tasks) = preg_split('/:/', $issue_it->get('MetricSpentHoursData'));
 			if ( !is_array($tasks) ) $tasks = array();

            if ( $issue_it->get('Fact') != $total ) {
                $parms['Fact'] = $total;
            }
            $tasks = join(',',
                array_filter(array_unique($tasks), function($value){
                    return $value > 0;
                })
            );
            if ( $issue_it->get('FactTasks') != $tasks ) {
                $parms['FactTasks'] = $tasks;
            }
            foreach( $customBuilders as $builder ) {
                $builder->build($issue_it, $parms);
            }
            if ( count($parms) > 0 ) {
                $parms['RecordModified'] = $issue_it->get('RecordModified');
				$registry->Store( $issue_it, $parms );
 			}
 			$issue_it->moveNext();
 		}
 	}
}