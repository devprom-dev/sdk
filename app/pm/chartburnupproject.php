<?php
 
include('common.php');
include "classes/plan/ReleaseModelMetricsBuilder.php";
 
// initial data
$total_workload = getSession()->getProjectIt()->getTotalWorkload();
$total_velocity = getSession()->getProjectIt()->getTeamVelocity();

// past data
$total = array( $total_workload );
$resolved = array( 0 );
 
$it = getFactory()->getObject('Release')->getRegistry()->Query(
	 		array (
	 				new ReleaseModelMetricsBuilder(),
	 				new ReleaseTimelinePredicate('past'),
	 				new FilterBaseVpdPredicate()
	 		)
 	);

$request = getFactory()->getObject('pm_ChangeRequest');
$request->addFilter( new FilterAttributePredicate('Project', getSession()->getProjectIt()->getId()) );

$data = getSession()->getProjectIt()->getMethodologyIt()
					->getEstimationStrategy()->getEstimation( $request, 'Estimation', 'PlannedRelease' );

foreach( $data as $release_id => $resolved_volume )
{
	if ( $release_id < 1 ) continue; 
	$total[] = $total_workload;
	$resolved[] = $resolved[count($resolved)-1] + $resolved_volume;
}
 
// make a prognosis 
$velocity = $total_velocity;
$nonplanned = $total[count($total)-1] - $resolved[count($resolved)-1];

if ( $velocity > 0 )
{
 	$left_iterations = ceil($nonplanned / $velocity);
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
header('Content-type: application/json; charset='.APP_ENCODING);

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
