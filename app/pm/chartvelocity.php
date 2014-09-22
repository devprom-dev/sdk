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
 $release = $model_factory->getObject('Iteration');
 
 $metrics_it = $release->getByRef('Version', $_REQUEST['version']);
 
 $values = array();
 $x_values = array();
 $x_names = array();
 
 for($i = 0; $i < $metrics_it->count(); $i++) 
 {
	array_push($values, $metrics_it->get('Velocity'));
	
	array_push($x_values, $i);
	array_push($x_names, $metrics_it->wintoutf8($metrics_it->getDisplayName()) );

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

 $graph = new VelocityGraph ( $x_names, 520, 100 );

 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$values, array(251, 159, 77), 2) );
 
 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$avg_values, array( 63, 136, 225), 1.5) );
 
 $graph->draw();
?>