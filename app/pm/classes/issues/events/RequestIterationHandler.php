<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class RequestIterationHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !$object_it->object instanceof Request ) return;
		if ( $kind != 'modify' ) return;

	    $data = $this->getRecordData();	    
	    if ( !array_key_exists('Iterations', $data) ) return;

	    $service = new StoreMetricsService();
    	$service->storeIssueMetrics(
			$object_it->object->getRegistry(),
			array (
				new FilterInPredicate($object_it->getId())
			)
		);
	}
}