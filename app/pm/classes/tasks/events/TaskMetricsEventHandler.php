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
        if ( $object_it->get('Release') != '' && $object_it->object->getAttributeType('Release') != '' ) {
            $iterationIt = $object_it->getRef('Release');
            getSession()->addCallbackDelayed(
                array(
                    'IterationMetrics' => $iterationIt->getId()
                ),
                function() use ( $iterationIt ) {
                    $iterationIt->storeMetrics();
                }
            );
        }
    }
}