<?php
 
 include('common.php');
 
 include "classes/plan/IterationModelMetricsBuilder.php";
 include "classes/plan/ReleaseModelMetricsBuilder.php";
 
 getSession()->addBuilder( new IterationModelMetricsBuilder() );
 getSession()->addBuilder( new ReleaseModelMetricsBuilder() );
 
 $release = $model_factory->getObject('Release');

 $release_it = $release->getExact($_REQUEST['release']);
 
 $iteration = $model_factory->getObject('Iteration');
 
 $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::PAST) );

 $it = $iteration->getByRef( 'Version', $release_it->getId() );

 // initial data
 $total = array( $release_it->get('Workload') );
 
 $recent_velocity = array( $release_it->get('Velocity') );

 $resolved = array( 0 );
 
 while ( !$it->end() )
 {
	$total[] = $release_it->get('Workload');
		
	$resolved[] = $resolved[count($resolved)-1] + $it->get('IterationEstimation');
	
	$recent_velocity[] = $it->get('Velocity');
	
	$it->moveNext();
 }
 
 // make a prognosis 
 $velocity = $release_it->get('Velocity');
 
 $nonplanned = $total[count($total)-1] - $resolved[count($resolved)-1];

 if ( $velocity == 0 )
 {
 	$velocity = $recent_velocity[count($recent_velocity)-1] <= 0
 		? max($recent_velocity) : $recent_velocity[count($recent_velocity)-1]; 
 }

 $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 
 	$iteration_duration = $methodology_it->getReleaseDuration() * $project_it->getDaysInWeek();
 	
 	if ( $velocity > 0 )
 	{
 		$left_iterations = ceil($nonplanned / $velocity / $iteration_duration);
 	}
 	else
 	{
 		$left_iterations = 1000;
 	}

 $prognosis = array();
 $index = count($total) - 1;
 
 for( $i = 0; $i < $left_iterations + 1; $i++, $index++ )
 {
 	$total[] = $total[count($total)-1]; 
 	
 	$devider = $i > 0 ? $nonplanned / $left_iterations * $i : 0;
 	
 	$prognosis[$index] = $resolved[count($resolved)-1] + $devider; 
 }
 
 header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
 header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
 header("Pragma: no-cache"); // HTTP/1.0
 header('Content-type: application/json; charset=windows-1251');

 $lines = array( 
	translate('Оценка') => $total, 
	translate('Выполнено') => $resolved,
	translate('Прогноз') => $prognosis 
 );

 $labels = array();
 foreach( $lines as $label => $points )
 {
	$data = array();
	foreach( $points as $key => $value )
	{
 		$data[] = "[".($key).",".round($value,1)."]";
	}
	$labels[] = '{"label":"'.$label.'","data":['.join(',',$data).']}';
 }

 echo '['.join(',',$labels).']';
 
?>