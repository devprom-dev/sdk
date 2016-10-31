<?php

namespace Devprom\ProjectBundle\Service\Project;

include_once SERVER_ROOT_PATH."pm/classes/product/persisters/FeatureMetricsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestMetricsPersister.php";

class StoreMetricsService
{
 	function execute( $project_it )
 	{
 		$this->storeProjectMetrics($project_it);

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
 	
 	function storeProjectMetrics( $project_it )
 	{
        getFactory()->resetCachedIterator($project_it->object);

        $methodology_it = $project_it->getMethodologyIt();
        $finishDate = '';

        if ( $methodology_it->HasReleases() ) {
            $version_it = getFactory()->getObject('Release')->getRegistry()->Query(
                array (
                    new \FilterAttributePredicate('Project', $project_it->getId()),
                    new \ReleaseTimelinePredicate('not-passed')
                )
            );
            while ( !$version_it->end() ) {
                $version_it->storeMetrics();
                $version_it->moveNext();
            }
            $velocity = $project_it->getVelocityDevider();

        }
        else if ( $methodology_it->HasPlanning() ) {
            $iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
                array (
                    new \FilterAttributePredicate('Project', $project_it->getId()),
                    new \IterationTimelinePredicate(\IterationTimelinePredicate::NOTPASSED)
                )
            );
            while ( !$iteration_it->end() ) {
                $iteration_it->storeMetrics();
                $iteration_it->moveNext();
            }
            $velocity = $project_it->getVelocityDevider();
        }
        else {
            $issue = getFactory()->getObject('Request');
            $requestsCount = $issue->getRegistry()->Count(
                array (
                    new \FilterVpdPredicate($project_it->get('VPD')),
                    new \StatePredicate('terminal'),
                    new \FilterModifiedAfterPredicate(
                        strftime('%Y-%m-%d', strtotime('-1 week', strtotime(date('Y-m-d'))))
                    )
                )
            );
            $leftRequests = $issue->getRegistry()->Count(
                array (
                    new \FilterVpdPredicate($project_it->get('VPD')),
                    new \StatePredicate('notterminal')
                )
            );
            $velocity = $requestsCount / 7;
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

        $metrics_registry = getFactory()->getObject('ProjectMetric')->getRegistry();
        foreach( $project_it->object->getAttributesByGroup('metrics') as $attribute ) {
            $metrics_registry->setMetric($attribute, $project_it->get($attribute));
        }
 	}
 	
 	function storeFeatureMetrics( $registry, $queryParms )
 	{
		$registry->setPersisters(array());
		$feature_it = $registry->Query($queryParms);

        getFactory()->resetCachedIterator($feature_it->object);
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
		$registry->setPersisters(array());
		$issue_it = $registry->Query($queryParms);

        getFactory()->resetCachedIterator($issue_it->object);
 		while( !$issue_it->end() )
 		{
			$parms = array();
 			if ( $issue_it->get('MetricDeliveryDate') != $issue_it->get('DeliveryDate') ) {
                $parms['DeliveryDate'] = $issue_it->get('MetricDeliveryDate');
            }

            list($total, $tasks) = preg_split('/:/', $issue_it->get('MetricSpentHoursData'));
            list($total_parent, $tasks_parent) = preg_split('/:/', $issue_it->get('MetricSpentHoursParentData'));
            $total += $total_parent;
            $tasks = join(',', array($tasks, $tasks_parent));
            if ( $issue_it->get('Fact') != $total ) {
                $parms['Fact'] = $total;
            }
            if ( $issue_it->get('FactTasks') != $tasks ) {
                $parms['FactTasks'] = $tasks;
            }

            if ( count($parms) > 0 ) {
                $parms['RecordModified'] = $issue_it->get('RecordModified');
				$registry->Store( $issue_it, $parms );
 			}
 			$issue_it->moveNext();
 		}
 	}
}