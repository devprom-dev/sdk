<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

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
	    
	    $service = new StoreMetricsService();
    	$service->storeFeatureMetrics($object_it->object->getRegistry()->Query(
    			array (
    					new FilterInPredicate($ids),
    					new FeatureMetricsPersister()
    			)
    		));
	}
}