<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

class MilestoneMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !$object_it->object instanceof Milestone ) return;
	    if ( $object_it->get('TraceRequests') == '' ) return;

	    $ids = array_filter(
                    preg_split('/,/',$object_it->get('TraceRequests')),
                    function($value) {
                        return $value > 0;
                    }
	    	    );

	    if ( count($ids) < 1 ) return;

	    $service = new StoreMetricsService();
    	$service->forceIssueMetrics(
			array (
				new FilterInPredicate($ids),
                new \StatePredicate('notresolved')
			)
		);
	}
}