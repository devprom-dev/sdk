<?php
 
 include('common.php');
 include('c_graph.php');

 switch ( strtolower($_REQUEST['background']) )
 {
 	case 'f3f3f6':
 		$background = array(243, 243, 246);
 		break;
 		
 	case 'fffff2':
 		$background = array(255, 255, 242);
 		break;
 		
 	default:
 		$background = array(255, 255, 255);
 }

 /////////////////////////////////////////////////////////////////////////////////
 class BurnDownChart extends GraphImage
 {
 	function getYStep() {
 		return $this->getMaxValue() > 7 ? round($this->getMaxValue() / 7) : 1;
 	}
	function getBackgroundColor() 
	{
		global $background;
		return imagecolorallocate ($this->getImage(), $background[0], $background[1], $background[2]);
	}
 }

 /////////////////////////////////////////////////////////////////////////////////
 $release = $model_factory->getObject('pm_Version');
 $metrics = $model_factory->getObject('pm_VersionBurndown');

 if ( $_REQUEST['version'] > 0 )
 {
 	$release_it = $release->getExact($_REQUEST['version']);
 }
 else
 {
 	$release_it = $release->getRegistry()->Query(
 			array (
 					new ReleaseTimelinePredicate('current'),
 					new FilterBaseVpdPredicate()
 			)
 		);
 }
 
 if ( $release_it->count() < 1 )
 {
 	$release_it = $release->getRegistry()->Query(array(new FilterBaseVpdPredicate()));
 }

 if ( $release_it->count() < 1 ) return;
 
 // refesh metrics for the current day
 //if ( !$release_it->IsFinished() )
 {
 	$release_it->storeBurndownSnapshot();
 }
 
 $start_date = $release_it->get_native('StartDate');
 $finish_date = $release_it->get_native('FinishDate');

 if ( $start_date == '' )
 {
 	$start_date = $release_it->get_native('EstimatedStartDate');
 }
 
 if ( $finish_date == '' )
 {
 	$finish_date = $release_it->get_native('EstimatedFinishDate');
 }

 $sql = "SELECT TO_DAYS('".$finish_date."') - TO_DAYS('".$start_date."') + 1 Duration," .
 		"       m.Workload, m.PlannedWorkload, m.SnapshotDays, " .
 		"       TO_DAYS('".$start_date."') StartDays, " .
 		"	    TO_DAYS(NOW()) - TO_DAYS('".$start_date."') OffsetDays, " .
 	    "       GREATEST(TO_DAYS(NOW()) - TO_DAYS('".$finish_date."') - 1, 0) AdditionalDays, ".
 		"		r.StartDate ".
 		"  FROM pm_Version r " .
 		"		LEFT OUTER JOIN pm_VersionBurndown m " .
 		"			ON r.pm_VersionId = m.Version " .
 		" WHERE r.pm_VersionId = ".$release_it->getId().
 		" ORDER BY m.SnapshotDays ASC LIMIT 1 ";

 $metrics_it = $metrics->createSQLIterator($sql);
 
 $duration = min($metrics_it->get('Duration'), 365);
 	
 $capacity = $metrics_it->get('PlannedWorkload') > 0 
 	? $metrics_it->get('PlannedWorkload') : $release_it->getPlannedTotalWorkload();

 $chartOffset = max(0, $metrics_it->get('SnapshotDays') - $metrics_it->get('StartDays'));
 $start_date = date(strftime($metrics_it->get('StartDate')));
 
 // get week days
 $daysinweek = $project_it->getDaysInWeek();
 $weekdays = array();

 for( $i = 0; $i < 7 - $daysinweek; $i++ )
 	$weekdays[] = ($daysinweek + $i + 1) == 7 ? 0 : ($daysinweek + $i + 1);
  
 $weekdaysduration = 0;
 for($i = $chartOffset; $i <= $duration; $i++, $k++) 
 {
 	$weekday = date('w', strtotime($i.' day', strtotime($start_date)));
 	if ( in_array($weekday, $weekdays) && $i < $duration ) $weekdaysduration++;
 }
 
 $devider = ($duration - $chartOffset - $weekdaysduration);
 
 $degradation = $devider > 0 ? $capacity / $devider : 0;

 $y1_values = array();
 $x1_values = array();

 // planned burndown line (red line)
 $k = 0; 
 
 for($i = 0; $i < $chartOffset; $i++) 
 {
	array_push($y1_values, $capacity);
	array_push($x1_values, $i);
 }

 for($i = $chartOffset; $i <= $duration; $i++, $k++) 
 {
 	array_push($y1_values, max($capacity - $k * $degradation, 0));
	array_push($x1_values, $i);
	
 	$weekday = date('w', strtotime($i.' day', strtotime($start_date)));
 	if ( in_array($weekday, $weekdays) ) $k--;
 }
 
 $y2_values = array();
 $x2_values = array();
 $y4_values = array();
 
 $sql = " SELECT t.SnapshotDays, MAX(t.Workload) Workload, " .
 		"		 MAX(t.PlannedWorkload) PlannedWorkload " .
 		"   FROM pm_VersionBurndown t " .
 		"  WHERE t.Version = " .$release_it->getId().
 		"  GROUP BY t.SnapshotDays ".
 		"  ORDER BY t.SnapshotDays ASC";

 $days_it = $metrics->createSQLIterator($sql);

 if ( $metrics_it->get('OffsetDays') >= 0 )
 {
	 for($k = 0; $k < $chartOffset + 1; $k++) 
	 {
		array_push($y2_values, $capacity);
		array_push($y4_values, $capacity);
		array_push($x2_values, $k);
	 }

	 $last_day = $days_it->get('SnapshotDays') == '' ? 
	 	$metrics_it->get('StartDays') : $days_it->get('SnapshotDays');
	 
	 $days = 0;
	 
	 while ( !$days_it->end() )
	 {
		array_push($y2_values, $days_it->get('Workload'));
		array_push($y4_values, $days_it->get('PlannedWorkload'));
	
		array_push($x2_values, $k + $days);
	
		if ( $days_it->get('SnapshotDays') <= $last_day + $days )
		{
			$days_it->moveNext();
		}
	
	 	$days++;
	 }

	 if ( !$release_it->IsCompleted() )
	 {
		 for($i = 0; $i < $metrics_it->get('AdditionalDays'); $i++, $days++) 
		 {
			array_push($y2_values, $y2_values[count($y2_values)-1]);
			array_push($y4_values, $y4_values[count($y4_values)-1]);
			array_push($x2_values, $k + $days);
		 }
	 }
	 
	 $y3_values = array();
	 $x3_values = array();
	
	 $days_it->moveToPos($days_it->count() - 1);
	 $approx_workload = 1;
	
	 if ( $degradation > 0 )
	 {
		$day_number = $k + $days - 1;
		$dg = 0;
		
	 	while( $approx_workload > 0 && $dg < 365 )
		{
		 	$approx_workload = max($days_it->get('Workload') 
		 		- $dg * $degradation, 0);
		 	
			if ( $approx_workload < 0 ) break;

		 	array_push($y3_values, $approx_workload);
			array_push($x3_values, $day_number);

		 	$weekday = date('w', strtotime($day_number.' day', strtotime($start_date)));
		 	if ( in_array($weekday, $weekdays) ) $dg--;
			
			$dg++;
			$day_number++;
		}
	 }
 }

 if ( $_REQUEST['json'] != '' )
 {
	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Pragma: no-cache"); // HTTP/1.0
	header('Content-type: application/json; charset=windows-1251');

	$y_values = array( 
		translate('Идеально') => $y1_values, 
		translate('Запланировано') => $y4_values, 
		translate('Фактически') => $y2_values );
	 
	$labels = array();
	foreach( $y_values as $label => $points )
	{
		$data = array();
		foreach( $points as $key => $value )
		{
	 		$time = strtotime($key.' day', strtotime($start_date)) * 1000;
	 		$data[] = "[".$time.",".round($value,1)."]";
		}
		$labels[] = '{"label":"'.$label.'","data":['.join(',',$data).']}';
	}

	if ( is_array($y3_values) )
	{
		$data = array();
		foreach( $y3_values as $key => $value )
		{
	 		$time = strtotime($x3_values[$key].' day', strtotime($start_date)) * 1000;
		 	$data[] = "[".$time.",".round($value,1)."]";
		}
		$labels[] = '{"label":"'.translate('Прогноз').'","data":['.join(',',$data).']}';
	}
	
	echo '['.join(',',$labels).']';
 	die();
 }
 
 $x_names = array();
 $sql_x_names = "SELECT ";

 if ( count($x3_values) == 0 )
 {
	$names_count = max( 
		count($x1_values) > 0 ? max($x1_values) : 0, 
		count($x2_values) > 0 ? max($x2_values) : 0 );
 }
 else
 {
 	$names_count = max( max( max($x3_values), max($x1_values) ), max($x2_values)) + 1;
 }
 
 for($i = 0; $i < $names_count + 2; $i++) 
 {
 	$current_day = date('d', strtotime($i.' day', strtotime($start_date)));
 	array_push($x_names, ' '.$current_day);
 }
 
 $graph = new BurnDownChart ( $x_names, 
 	$_REQUEST['width'] > 0 ? $_REQUEST['width'] : 260, 
 	$_REQUEST['height'] > 0 ? $_REQUEST['height'] : 150 );
 
 if ( max($y1_values) > 0 )
 {
 	 // ideal brundown line
	 $graph->addGraphLine( 
	 	new GraphLine( $x1_values, 
	 		$y1_values, array( 225, 63, 63 ), 2) );
	
	 // planned workload
	 $graph->addGraphLine( 
	 	new GraphLine( $x2_values, 
	 		$y4_values, array( 247, 239, 59 ), 2) );
	
	 // actual workload
	 $graph->addGraphLine( 
	 	new GraphLine( $x2_values, 
	 		$y2_values, array( 63, 225, 63 ), 3) );
	
	 // prognosis for actual workload
	 $graph->addGraphLine( 
	 	new GraphLineDashed( $x3_values, 
	 		$y3_values, array( 63, 225, 63 ), 2) );
 }

 $graph->draw();
 
?>