<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

class RequestMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
        if ( $object_it->object instanceof RequestTraceMilestone ) {
            $object_it = $object_it->getRef('ChangeRequest');
            $kind = TRIGGER_ACTION_MODIFY;
        }

        if ( $object_it->object instanceof Request ) {
            if ( in_array($kind, array(TRIGGER_ACTION_ADD, TRIGGER_ACTION_MODIFY)) ) {
                $this->updateRequestMetrics($object_it->getId());
            }
            $this->updateFeatureMetrics($object_it);
            $this->updatePlanMetrics($object_it);

            if ( $object_it->get('Owner') != '' ) {
                $service = new StoreMetricsService();
                $service->forceUsersMetrics(
                    array(
                        new \FilterInPredicate($object_it->get('Owner'))
                    )
                );
            }
        }
	}

	protected function updateRequestMetrics( $requestId )
    {
        $service = new StoreMetricsService();
        $service->forceIssueMetrics(
            array(
                new \FilterInPredicate(array($requestId)),
                new \StatePredicate('notresolved')
            )
        );
    }

	protected function updateFeatureMetrics( $object_it )
    {
        if ( $object_it->get('Function') == '' ) return;
        if ( $object_it->object->getAttributeType('Function') == '' ) return;

        $featureIt = $object_it->getRef('Function')->copy();
        $ids = array_filter(
            preg_split('/,/',$featureIt->get('ParentPath')),
            function($value) {
                return $value > 0;
            }
        );
        if ( count($ids) < 1 ) return;

        $service = new StoreMetricsService();
        $service->storeFeatureMetrics(
            getFactory()->getObject('Feature')->getRegistryBase(),
            array (
                new FilterInPredicate($ids),
                new FeatureMetricsPersister()
            )
        );
    }

    protected function updatePlanMetrics( $object_it )
    {
        if ( $object_it->get('Iteration') != '' && $object_it->object->getAttributeType('Iteration') != '' ) {
            $iterationIt = getFactory()->getObject('Iteration')->getExact($object_it->get('Iteration'));
            $iterationIt->storeMetrics();
        }
        if ( $object_it->get('PlannedRelease') != '' && $object_it->object->getAttributeType('PlannedRelease') != '' ) {
            $releaseIt = getFactory()->getObject('Release')->getExact($object_it->get('PlannedRelease'));
            $releaseIt->storeMetrics();
        }
    }
}