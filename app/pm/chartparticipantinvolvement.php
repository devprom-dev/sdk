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

 $metrics_it = $participant_it->getMetrics($release_it, 
 	array('DevelopmentInvolvement', 'TestingInvolvement'));
 
 $dev_values = array();
 $tst_values = array();
 $x_values = array();
 $x_names = array();
 
 if ( !is_null($metrics_it) )
 {
	 for($i = 0; $i < $metrics_it->count(); $i++) 
	 {
		array_push($dev_values, $metrics_it->get('DevelopmentInvolvement'));
		array_push($tst_values, $metrics_it->get('TestingInvolvement'));
		
		array_push($x_values, $i);
		array_push($x_names, $metrics_it->wintoutf8(
			$metrics_it->get('Caption').".".$metrics_it->get('ReleaseNumber')) );
	
		$metrics_it->moveNext();
	 }
 }

 $graph = new VelocityGraph ( $x_names, 250, 100 );

 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$tst_values, array(251, 159, 77), 2) );
 
 $graph->addGraphLine( new GraphLine(
 	$x_values, 
 	$dev_values, array( 63, 136, 225), 1.5) );
 
 $graph->draw();
?>