<?php
namespace Devprom\ProjectBundle\Service\Project;

include_once SERVER_ROOT_PATH . "pm/classes/project/MetricIssueBuilder.php";
include_once SERVER_ROOT_PATH . "pm/classes/product/persisters/FeatureMetricsPersister.php";
include_once SERVER_ROOT_PATH . "pm/classes/participants/predicates/UserHasIncompleteWorkPredicate.php";

class StoreMetricsService
{
 	function execute( $project_it, $force = false )
 	{
 		$this->storeProjectMetrics(
 		    $project_it,
            getFactory()->getObject('Release')->getRegistryBase()->Query(
                array (
                    new \FilterAttributePredicate('Project', $project_it->getId()),
                    $force
                        ? new \FilterDummyPredicate()
                        : new \ReleaseTimelinePredicate('not-passed')
                )
            ),
            getFactory()->getObject('Iteration')->getRegistryBase()->Query(
                array (
                    new \FilterAttributePredicate('Project', $project_it->getId()),
                    new \FilterAttributeNullPredicate('Version'),
                    $force
                        ? new \FilterDummyPredicate()
                        : new \IterationTimelinePredicate(\IterationTimelinePredicate::NOTPASSED)
                )
            )
        );

		$registry = getFactory()->getObject('Request')->getRegistryBase();
        $customBuilders = getSession()->getBuilders('MetricIssueBuilder');
        foreach( $customBuilders as $builder ) {
            $builder->buildAll($registry, array (
                new \StatePredicate('notresolved'),
                new \FilterVpdPredicate($project_it->get('VPD'))
            ));
        }

        $registry = getFactory()->getObject('Task')->getRegistryBase();
        $customBuilders = getSession()->getBuilders('MetricTaskBuilder');
        foreach( $customBuilders as $builder ) {
            $builder->buildAll($registry, array (
                new \StatePredicate('notresolved'),
                new \FilterVpdPredicate()
            ));
        }

		$registry = getFactory()->getObject('Feature')->getRegistryBase();
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

        $stage = getFactory()->getObject('Stage');
        $max = new \AggregateBase( 'Project', 'EstimatedFinishDate', 'MAX' );
        $stage->addAggregate($max);
        $min = new \AggregateBase( 'Project', 'EstimatedStartDate', 'MIN' );
        $stage->addAggregate($min);
        $finishDate = $stage->getAggregated()->get($max->getAggregateAlias());
        $startDate = $stage->getAggregated()->get($min->getAggregateAlias());

        $issue = getFactory()->getObject('Request');
        $max = new \AggregateBase( 'VPD', 'EstimatedFinishDate', 'MAX' );
        $issue->addAggregate($max);
        $min = new \AggregateBase( 'VPD', 'EstimatedFinishDate', 'MIN' );
        $issue->addAggregate($min);
        $finishDate = max($finishDate, $issue->getAggregated()->get($max->getAggregateAlias()));
        if ( $issue->getAggregated()->get($min->getAggregateAlias()) != '' ) {
            $startDate = min($startDate, $issue->getAggregated()->get($min->getAggregateAlias()));
        }

        $task = getFactory()->getObject('Task');
        $max = new \AggregateBase( 'VPD', 'EstimatedFinishDate', 'MAX' );
        $task->addAggregate($max);
        $min = new \AggregateBase( 'VPD', 'EstimatedFinishDate', 'MIN' );
        $task->addAggregate($min);
        $finishDate = max($finishDate, $task->getAggregated()->get($max->getAggregateAlias()));
        if ( $task->getAggregated()->get($min->getAggregateAlias()) != '' ) {
            $startDate = min($startDate, $task->getAggregated()->get($min->getAggregateAlias()));
        }

		$project_it->object->setNotificationEnabled(false);
 		$project_it->object->modify_parms($project_it->getId(), 
            array (
                'EstimatedStartDate' => $startDate,
                'EstimatedFinishDate' => $finishDate,
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

 	public function forceIssueMetrics( $queryParms )
    {
        $registry = getFactory()->getObject('Request')->getRegistryBase();

        $customBuilders = getSession()->getBuilders('MetricIssueBuilder');
        foreach( $customBuilders as $builder ) {
            $builder->buildAll($registry,
                array_merge(
                    $queryParms,
                    array (
                        new \FilterVpdPredicate()
                    )
                )
            );
        }
    }

    public function forceTaskMetrics( $queryParms )
    {
        $registry = getFactory()->getObject('Task')->getRegistryBase();

        $customBuilders = getSession()->getBuilders('MetricTaskBuilder');
        foreach( $customBuilders as $builder ) {
            $builder->buildAll($registry, array_merge(
                $queryParms,
                array (
                    new \FilterVpdPredicate()
                )
            ));
        }
    }

    public function executeWorkers()
    {
        $this->forceUsersMetrics(array(
            new \UserHasIncompleteWorkPredicate()
        ));
    }

    public function forceUsersMetrics( $queryParms )
    {
        $registry = getFactory()->getObject('User')->getRegistryBase();
        $customBuilders = getSession()->getBuilders('MetricUserBuilder');
        foreach( $customBuilders as $builder ) {
            $builder->buildAll($registry, $queryParms);
        }
    }
}