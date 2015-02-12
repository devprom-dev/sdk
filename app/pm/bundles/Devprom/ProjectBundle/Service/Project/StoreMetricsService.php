<?php

namespace Devprom\ProjectBundle\Service\Project;

include_once SERVER_ROOT_PATH."pm/classes/product/persisters/FeatureMetricsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestMetricsPersister.php";

class StoreMetricsService
{
 	function execute( $project_it )
 	{
 		$this->storeProjectMetrics($project_it);
 		
		$this->storeFeatureMetrics( 
				getFactory()->getObject('Feature')->getRegistry()->Query(
		 				array (
		 						new \FilterVpdPredicate(array($project_it->get('VPD'))),
		 						new \FeatureMetricsPersister()
		 				)
 				)
		 	);

 		$this->storeIssueMetrics( 
				getFactory()->getObject('Request')->getRegistry()->Query(
		 				array (
		 						new \FilterVpdPredicate(array($project_it->get('VPD'))),
		 						new \StatePredicate('notresolved'),
		 						new \RequestMetricsPersister()
		 				)
 				)
		 	);

 		$this->storeIssueMetrics( 
				getFactory()->getObject('Request')->getRegistry()->Query(
		 				array (
		 						new \FilterVpdPredicate(array($project_it->get('VPD'))),
		 						new \StatePredicate('notresolved'),
		 						new \RequestDependencyFilter('duplicates,implemented,blocked'),
		 						new \RequestMetricsPersister()
		 				)
 				)
		 	);
 	}
 	
 	function storeProjectMetrics( $project_it )
 	{
 		if ( !$project_it->getMethodologyIt()->HasReleases() )
 		{
 			$request = getFactory()->getObject('Request');
 			$request->addFilter( new \StatePredicate('terminal') );
 			
 			$aggregate = new \AggregateBase( 'Project', 'LifecycleDuration', 'SUM' );
			$request->addAggregate($aggregate);
			
			$avg_lead_time = $request->getAggregated()->get($aggregate->getAggregateAlias());
			$velocity = $avg_lead_time <= 0 ? 0 : (1 / ($avg_lead_time / 24));
 		}
 		else
 		{
			$version_it = getFactory()->getObject('Release')->getRegistry()->Query(
					array (
							new \FilterAttributePredicate('Project', $project_it->getId()),
							new \ReleaseTimelinePredicate('not-passed')
					)
			);
	
			while ( !$version_it->end() )
			{
				$version_it->storeMetrics();
				$version_it->moveNext();
			}
			
			$velocity = $project_it->getVelocityDevider();
 		}
		
		$project_it->object->setNotificationEnabled(false);
 		$project_it->object->modify_parms($project_it->getId(), 
				array (
						'Rating' => $velocity,
						'RecordVersion' => $project_it->get('RecordVersion')
				)
		);
 	}
 	
 	function storeFeatureMetrics( $feature_it )
 	{
 		$feature_it->object->setNotificationEnabled(false);
 		while( !$feature_it->end() )
 		{
	 		$feature_it->object->modify_parms(
	 				$feature_it->getId(),
	 				array (
	 						'Estimation' => $feature_it->get('MetricEstimation'),
	 						'EstimationLeft' => $feature_it->get('MetricEstimationLeft'),
	 						'Workload' => $feature_it->get('MetricWorkload'),
	 						'StartDate' => $feature_it->get('MetricStartDate'),
	 						'DeliveryDate' => $feature_it->get('MetricDeliveryDate'),
	 						'RecordVersion' => $feature_it->get('RecordVersion')
	 				)
	 		);
 			$feature_it->moveNext();
 		}
 	}
 	
 	function storeIssueMetrics( $issue_it )
 	{
		$issue_it->object->setNotificationEnabled(false);
 		while( !$issue_it->end() )
 		{
 			if ( $issue_it->get('MetricDeliveryDate') != $issue_it->get('DeliveryDate') )
 			{ 
		 		$issue_it->object->modify_parms(
		 				$issue_it->getId(),
		 				array (
		 						'DeliveryDate' => $issue_it->get('MetricDeliveryDate'),
		 						'RecordVersion' => $issue_it->get('RecordVersion')
		 				)
		 		);
 			}
 			$issue_it->moveNext();
 		}
 	}
}