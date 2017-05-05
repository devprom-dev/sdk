<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

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
            $object_it->getRef('Release')->storeMetrics();
        }
    }
}