<?php

$uml = "@startuml".PHP_EOL.PHP_EOL;
$uml .= "skinparam state {".PHP_EOL.
  			"BackgroundColor<<Complete>> Yellow".PHP_EOL.
			"BackgroundColor<<Last>> Orange".PHP_EOL.
		"}".PHP_EOL;

$rows = array_reverse($rows);

$timeTable = array();
$totalSpent = array();
foreach( $rows as $row ) {
	$key = $row['datetime'] .'#'. $row['date'].', '.$row['author'];
	if ( $row['transition'] != '' ) {
		$key .= ', '.$row['transition'];
	}
	$html2text = new \Html2Text\Html2Text($row['comment'], array('width'=>0));
	$commentText = $html2text->getText();
	if ( $commentText != '' ) {
		$key .= '<br/> '.$commentText;
	}
	$timeTable[$key][$row['state-ref']] += $row['duration-value'];
	$totalSpent[$row['state-ref']] += $row['duration-value'];
}

$lastIndex = array_pop(array_keys($rows));
foreach( $rows as $index => $row ) {
	$uml .= "state " . '"' . str_replace(' ', '\n', $row['state']) . '" as ' . $row['state-ref'] . " <<".($index == $lastIndex ? "Last" : "Complete").">>" . PHP_EOL;
	if ( $row['duration'] != '' && $row['duration'] != '0' ) {
		$uml .= $row['state-ref'] .  ' : ' . $row['duration'] . PHP_EOL;
	}
}

if ( $placement == 'bottom' ) {
	$notePlacement = "bottom";
	$arrowPlacement = "-right->";
}
else {
	$notePlacement = "right";
	$arrowPlacement = "-down->";
}

$row = array_shift($rows);
$prevState = $row['state-ref'];
$arrowsMap = array(
    "-right->" => "-left->",
    "-left->" => "-right->"
);
$statesPassed = array();

foreach( $rows as $row ) {
    $statesPassed[$prevState] = '';
    $statesChainLength = count($statesPassed);
    if ( $statesChainLength % 6 === 0 || array_key_exists($row['state-ref'], $statesPassed) ) {
        $arrowPlacement = $arrowsMap[$arrowPlacement];
    }
    $arrow = $arrowPlacement;
    if ( $statesChainLength % 6 === 0 ) {
        $arrow = '-down->';
    }
	$uml .= $prevState . " ".$arrow." " . $row['state-ref'];
	$uml .= PHP_EOL;
	$prevState = $row['state-ref'];
}
$uml .= "@enduml";

$url = trim(defined('PLANTUML_SERVER_URL') ? PLANTUML_SERVER_URL : 'http://plantuml.com', "/ ");
$url .= '/plantuml/img/'.encode64(gzdeflate($uml, 9));

echo '<img class="workflow-image" src="'.$url.'">';

echo '<br/>';
if ( $lastComment != '' ) {
	echo '<br/>';
	echo '<div class="alert alert-blocked">'.$lastComment.'</div>';
}
echo '<br/>';
echo '<table class="table">';
echo '<tr>';
echo '<td>'.text(2270).'</td>'.
$stateIt->moveFirst();
while( !$stateIt->end() ) {
	echo '<td>'.$stateIt->getDisplayName().' ('.getSession()->getLanguage()->getDurationWording($totalSpent[$stateIt->get('ReferenceName')]).')</td>';
	$stateIt->moveNext();
}
echo '</tr>';
$width = max(1,round(100 / ($stateIt->count() + 2), 0));
foreach( $timeTable as $author => $durations ) {
	echo '<tr>';
		echo '<td>'.array_pop(preg_split('/#/', $author)).'</td>'.
		$stateIt->moveFirst();
		while( !$stateIt->end() ) {
			echo '<td width="'.$width.'%">'.getSession()->getLanguage()->getDurationWording($durations[$stateIt->get('ReferenceName')]).'</td>';
			$stateIt->moveNext();
		}
	echo '</tr>';
}
echo '</table>';
echo str_replace('%1', $lifecycle, text(2301));