<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

class RequestMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
        if ( !$object_it->object instanceof Request ) return;

        if ( in_array($kind, array(TRIGGER_ACTION_ADD, TRIGGER_ACTION_MODIFY)) ) {
            $this->updateRequestMetrics($object_it->getId());
        }
        $this->updateFeatureMetrics($object_it);
        $this->updatePlanMetrics($object_it);
	}

	protected function updateRequestMetrics( $requestId )
    {
        getSession()->addCallbackDelayed(
            array(
                'RequestMetrics' => $requestId
            ),
            function () use ($requestId) {
                $service = new StoreMetricsService();
                $request = new Request();

                $service->storeIssueMetrics(
                    $request->getRegistry(),
                    array(
                        new FilterInPredicate(array($requestId)),
                        new RequestMetricsPersister()
                    )
                );
            }
        );
    }

	protected function updateFeatureMetrics( $object_it )
    {
        if ( $object_it->get('Function') == '' ) return;
        if ( $object_it->object->getAttributeType('Function') == '' ) return;

        $featureIt = $object_it->getRef('Function')->copy();
        getSession()->addCallbackDelayed(
            array(
                'FeatureMetrics' => $object_it->get('Function')
            ),
            function() use ( $featureIt ) {
                $ids = array_filter(
                    preg_split('/,/',$featureIt->get('ParentPath')),
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
        );
    }

    protected function updatePlanMetrics( $object_it )
    {
        if ( $object_it->get('Iteration') != '' && $object_it->object->getAttributeType('Iteration') != '' ) {
            $iterationIt = $object_it->getRef('Iteration');
            getSession()->addCallbackDelayed(
                array(
                    'IterationMetrics' => $iterationIt->getId()
                ),
                function() use ( $iterationIt ) {
                    $iterationIt->storeMetrics();
                }
            );
        }
        if ( $object_it->get('PlannedRelease') != '' && $object_it->object->getAttributeType('PlannedRelease') != '' ) {
            $releaseIt = $object_it->getRef('PlannedRelease');
            getSession()->addCallbackDelayed(
                array(
                    'ReleaseMetrics' => $releaseIt->getId()
                ),
                function() use ( $releaseIt ) {
                    $releaseIt->storeMetrics();
                }
            );
        }
    }
}