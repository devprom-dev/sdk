<?php
 
 include('common.php');
 include('c_graph.php');

 include "classes/plan/IterationModelMetricsBuilder.php";
 
 /////////////////////////////////////////////////////////////////////////////////
 class VelocityGraph extends GraphImage
 {
	function getBackgroundColor() {
		return imagecolorallocate ($this->im, 255, 255, 255);
	}
 }

 /////////////////////////////////////////////////////////////////////////////////
 getSession()->addBuilder( new IterationModelMetricsBuilder() );
 
 $release = $model_factory->getObject('pm_Release');
 
 $metrics_it = $release->getByRef('Version', $_REQUEST['version']);
 
 $values = array();
 $x_values = array();
 $x_names = array();
 
 for($i = 0; $i < $metrics_it->count(); $i++) 
 {
	array_push($values, max(round($metrics_it->get('RequestEstimationError'), 0), 0));
	
	array_push($x_values, $i);
	array_push($x_names, $metrics_it->wintoutf8(
		$metrics_it->get('Caption').".".$metrics_it->get('ReleaseNumber')) );

	$metrics_it->moveNext();
 }
 
 $real_values = $values;
 
 $avg_values = array();
 $average = array_sum($real_values) / count($real_values);
 
 for($i = 0; $i < $metrics_it->count(); $i++) 
 {
	array_push($avg_values, $average);
 }

 $graph = new VelocityGraph ( $x_names, 300, 100 );

 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$values, array(251, 159, 77), 2) );
 
 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$avg_values, array( 63, 136, 225), 1.5) );
 
 $graph->draw();
?>