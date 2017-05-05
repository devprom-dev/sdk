<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

class RequestMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
        if ( !$object_it->object instanceof Request ) return;

        $this->updateFeatureMetrics($object_it);
        $this->updatePlanMetrics($object_it);
	}

	protected function updateFeatureMetrics( $object_it )
    {
        if ( $object_it->get('Function') == '' ) return;
        if ( $object_it->object->getAttributeType('Function') == '' ) return;

        $ids = array_filter(
            preg_split('/,/',$object_it->getRef('Function')->get('ParentPath')),
            function($value) {
                return $value > 0;
            }
        );
        if ( count($ids) < 1 ) return;

        $service = new StoreMetricsService();
        $service->storeFeatureMetrics(
            getFactory()->getObject('Feature')->getRegistry(),
            array (
                new FilterInPredicate($ids),
                new FeatureMetricsPersister()
            )
        );
    }

    protected function updatePlanMetrics( $object_it )
    {
        if ( $object_it->get('Iteration') != '' && $object_it->object->getAttributeType('Iteration') != '' ) {
            $object_it->getRef('Iteration')->storeMetrics();
        }
        if ( $object_it->get('PlannedRelease') != '' && $object_it->object->getAttributeType('PlannedRelease') != '' ) {
            $object_it->getRef('PlannedRelease')->storeMetrics();
        }
    }
}