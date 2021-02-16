<?php

class TaskMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1)
	{
        if ( !$object_it->object instanceof Task ) return;
        $this->updatePlanMetrics($object_it);
	}

    protected function updatePlanMetrics( $object_it )
    {
        if ( $object_it->get('Release') != '' ) {
            $iterationIt = getFactory()->getObject('Iteration')->getExact($object_it->get('Release'));
            register_shutdown_function(function() use ( $iterationIt ) {
                    $iterationIt->storeMetrics();
                }
            );
        }
    }
}