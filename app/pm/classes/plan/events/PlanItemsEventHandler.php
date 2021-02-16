<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

class PlanItemsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object instanceof Release ) {
	        $this->buildProjectMetrics($object_it->copy());
        }
        if ( $object_it->object instanceof Iteration ) {
            $this->buildProjectMetrics($object_it->copy());
        }
	}

	function buildProjectMetrics($object_it)
    {
        if ( getSession()->getUserIt()->getId() < 1 ) return;

        $projectId = getSession()->getProjectIt();
        register_shutdown_function( function() use ( $projectId, $object_it ) {
                $service = new \Devprom\ProjectBundle\Service\Project\StoreMetricsService();
                $service->storeProjectMetrics(
                    $projectId,
                    $object_it instanceof \ReleaseIterator ? $object_it : null,
                    $object_it instanceof \IterationIterator ? $object_it : null
                );
            }
        );
    }
}