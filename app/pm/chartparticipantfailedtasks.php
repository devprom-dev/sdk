<?php
 
 include('common.php');
 include('c_graph.php');

 /////////////////////////////////////////////////////////////////////////////////
 class VelocityGraph extends GraphImage
 {
	function getBackgroundColor() {
		return imagecolorallocate ($this->im, 255, 255, 255);
	}
 }

 /////////////////////////////////////////////////////////////////////////////////
 $participant = $model_factory->getObject('pm_Participant');
 $participant_it = $participant->getExact($_REQUEST['participant']);

 $release = $model_factory->getObject('pm_Release');
 $release_it = $release->getByRef('Version', $_REQUEST['version']);

 $metrics_it = $participant_it->getMetrics($release_it, array('FailedTasks'));
 
 $values = array();
 $x_values = array();
 $x_names = array();
 
 if ( !is_null($metrics_it) )
 {
	 for($i = 0; $i < $metrics_it->count(); $i++) 
	 {
		array_push($values, $metrics_it->get('FailedTasks'));
		
		array_push($x_values, $i);
		array_push($x_names, $metrics_it->wintoutf8(
			$metrics_it->get('Caption').".".$metrics_it->get('ReleaseNumber')) );
	
		$metrics_it->moveNext();
	 }
 }
 
 $graph = new VelocityGraph ( $x_names, 250, 100 );

 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$values, array(251, 159, 77), 2) );
 
 $graph->draw();
?>