<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class RequestFeatureUpdateMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !$object_it->object instanceof Request ) return;
	    if ( $object_it->get('Function') == '' ) return;

	    $ids = array_filter(
    				preg_split('/,/',$object_it->getRef('Function')->get('ParentPath')), 
		    		function($value) {
		    				return $value > 0;
		    		}
	    	);

	    if ( count($ids) < 1 ) return;

	    $service = new StoreMetricsService();
    	$service->storeFeatureMetrics(getFactory()->getObject('Feature')->getRegistry()->Query(
    			array (
    					new FilterInPredicate($ids),
    					new FeatureMetricsPersister()
    			)
    		));
	}
}