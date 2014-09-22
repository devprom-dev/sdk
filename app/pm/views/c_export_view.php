<?php

 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class IteratorExportStageTypes extends IteratorExport
 {
	function export()
	{
		global $_REQUEST, $model_factory;
		
	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");
		header('Content-Type: application/json; charset=utf-8');

		$iteration = $model_factory->getObject('Iteration');
		$iteration_it = $iteration->getExact( $_REQUEST['iteration'] );
		
		if ( $iteration_it->getId() < 1 ) return;
		
 		$tasktype = $model_factory->getObject('pm_TaskType');

 		$tasktype->addFilter( new TaskTypePlannablePredicate() );
		$tasktype->addFilter( new TaskTypeStageRelatedPredicate(
 			$iteration_it->get('ProjectStage')) );
		
		$tasktype_it = $tasktype->getAll();
 		$objects = array();
 		
		while ( !$tasktype_it->end() )
		{
 			array_push( $objects, "{".'"TaskType":"'.$tasktype_it->getId().'"'."}" );
			$tasktype_it->moveNext();
		}

 		echo '['.join($objects, ',').']';
 	}
 }

?>