<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class RequestIterationHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !$object_it->object instanceof Request ) return;

	    $data = $this->getRecordData();	    
	    if ( !array_key_exists('Iterations', $data) ) return;

	    $iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
	    		array ( new FilterInPredicate(preg_split('/,/',$data['Iterations'])) )
	    );
	    if ( $iteration_it->getId() < 1 ) return;

	    $task_it = $object_it->getRef('OpenTasks');
	    while( !$task_it->end() )
	    {
	    	$task_it->object->modify_parms($task_it->getId(),
	    			array (
	    					'Release' => $iteration_it->getId() 
	    			)
	    	);
	    	$task_it->moveNext();
	    }
	    
	    $service = new StoreMetricsService();
    	$service->storeIssueMetrics($object_it);
	}
}