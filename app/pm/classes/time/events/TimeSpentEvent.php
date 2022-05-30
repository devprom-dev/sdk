<?php

class TimeSpentEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'pm_Activity' ) return;

		$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
			array ( new FilterInPredicate($object_it->get('Task')) )
		);
		if ( $task_it->get('ChangeRequest') != '' )
		{
		    $requestId = $task_it->get('ChangeRequest');
            register_shutdown_function(function() use ( $requestId ) {
                    $service = new \Devprom\ProjectBundle\Service\Project\StoreMetricsService();
                    $service->forceIssueMetrics(
                        array (
                            new FilterInPredicate(array($requestId))
                        )
                    );
                }
            );
		}
	}
}