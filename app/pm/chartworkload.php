<?php
 
 require_once('common.php');
 require_once('c_graph.php');

 $participant = $_REQUEST['participant'];
 $period_end = $_REQUEST['period_end'];
 $period_begin = $_REQUEST['period_begin'];
 $num_days = $_REQUEST['num_days'];

 $part = $model_factory->getObject('Participant');
 $part_it = $part->getExact($participant);

 $sql = "SELECT TO_DAYS('".$period_end."') - TO_DAYS('".$period_begin."') + 1, DAYOFMONTH('".$period_begin."'), ".
 		" MONTH('".$period_begin."'), YEAR('".$period_begin."') ";
 $r2 = DAL::Instance()->Query($sql);
 $data = mysql_fetch_array($r2);
 $num_days = $data[0];

 $values = array();
 $x_values = array();
 $x_names = array();
 
 for($i = 0; $i < $num_days; $i++) {
 	array_push($values, $part_it->get('Capacity'));
 	array_push($x_values, $i);
	array_push($x_names, strftime('%d.%m', mktime(0,0,0,$data[2],$data[1]+$i,$data[3])));
 }

 $graph = new GraphImage ( $x_names, 420 );

 $graph->addGraphLine( 
 	new GraphLine($x_values, $values, array( 63, 136, 225), 1) );
 
 $fact_it = $part->getFactByDays( $participant, $period_begin, $period_end );
 
 $y_values = array();
 
 for($i = 0; $i < $num_days; $i++) {
	array_push($y_values, 0);
 }
 for($i = 0; $i < $fact_it->count(); $i++) {
 	for($j = $fact_it->get('BeginDay'); $j <= $fact_it->get('EndDay'); $j++) {
		$y_values[$j] += ($fact_it->get('Fact') == '' ? 0 : $fact_it->get('Fact'));
	}
 	$fact_it->moveNext();
 }
 $graph->addGraphLine( new GraphLine($x_values, $y_values, array(251, 159, 77), 3) );
 
 $graph->draw();
?>