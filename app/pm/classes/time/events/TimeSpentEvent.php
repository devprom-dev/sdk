<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;


class TimeSpentEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'pm_Activity' ) return;

		$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
			array ( new FilterInPredicate($object_it->get('Task')) )
		);
		if ( $task_it->get('ChangeRequest') != '' ) {
			$service = new StoreMetricsService();
			$request = new Request();

			$service->storeIssueMetrics(
				$request->getRegistry(),
				array (
					new FilterInPredicate(array($task_it->get('ChangeRequest'))),
					new RequestMetricsPersister()
				)
			);
		}
	}
}