<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

class PlanItemsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object instanceof Release ) {
	        $this->buildProjectMetrics();
        }
        if ( $object_it->object instanceof Iteration ) {
            $this->buildProjectMetrics();
        }
	}

	function buildProjectMetrics()
    {
        if ( getSession()->getUserIt()->getId() < 1 ) return;

        $projectId = getSession()->getProjectIt();
        getSession()->addCallbackDelayed(
            array(
                'ProjectMetrics' => $projectId
            ),
            function() use ( $projectId ) {
                $service = new StoreMetricsService();
                $service->storeProjectMetrics($projectId, true);
            }
        );
    }
}