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
 $release->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::CURRENT) );
 $release->addFilter( new IterationReleasePredicate($_REQUEST['version']) );
 
 $release_it = $release->getAll();

 $metrics_it = $participant_it->getMetrics($release_it, array('Efficiency'));
 
 $values = array();
 $x_values = array();
 $x_names = array();
 
 if ( !is_null($metrics_it) )
 {
	 for($i = 0; $i < $metrics_it->count(); $i++) 
	 {
		array_push($values, $metrics_it->get('Efficiency'));
		
		array_push($x_values, $i);
		array_push($x_names, $metrics_it->wintoutf8(
			$metrics_it->get('Caption').".".$metrics_it->get('ReleaseNumber')) );
	
		$metrics_it->moveNext();
	 }

	 $real_values = array_diff($values, array(0));
	 
	 $avg_values = array();
	 if ( count($real_values) > 0 )
	 {
	 	$average = array_sum($real_values) / count($real_values);
	 }
	 else
	 {
	 	$average = 0;
	 }
	 
	 for($i = 0; $i < $metrics_it->count(); $i++) 
	 {
		array_push($avg_values, $average);
	 }
 }
 
 $graph = new VelocityGraph ( $x_names, 250, 100 );

 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$values, array(251, 159, 77), 2) );
 
 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$avg_values, array( 63, 136, 225), 1.5) );
 
 $graph->draw();
?>