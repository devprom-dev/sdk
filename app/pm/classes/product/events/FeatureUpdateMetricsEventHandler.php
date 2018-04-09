<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;



class FeatureUpdateMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !$object_it->object instanceof Feature ) return;
	    
	    $was_data = $this->getWasData();
	    $ids = array_filter(
		    		array_merge(
		    				preg_split('/,/',$object_it->get('ParentPath')), 
		    				preg_split('/,/',$was_data['ParentPath'])
		    		), function($value) {
		    				return $value > 0;
		    		}
	    	);
	    if ( count($ids) < 1 ) return;

        getSession()->addCallbackDelayed(
            array(
                'FeatureMetrics' => $object_it->getId()
            ),
            function() use ( $ids ) {
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
}