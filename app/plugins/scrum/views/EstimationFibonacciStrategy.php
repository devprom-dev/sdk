<?php
include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

class EstimationFibonacciStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text('scrum15');
	}
	
	function getEstimationText()
	{
		return text('scrum11');
	}
	
	function getDimensionText( $value )
	{
		return str_replace("%1", $value, text('scrum14'));
	}
	
	function hasEstimationValue()
	{
		return true;
	}

	function getFilterScale()
	{
		return array(
			'0:3' => translate('Простые'),
			'5:21' => translate('Средние'),
			' 34' => translate('Сложные')
		);
	}
	
	function getScale()
	{
		return array (
 			' 0' => 0,
		    ' 1' => 1,
 			' 2' => 2,
 			' 3' => 3,
 			' 5' => 5,
 			' 8' => 8,
 			' 13' => 13,
 			' 21' => 21,
 			' 34' => 34,
 			' 55' => 55,
 			' 89' => 89,
 			' 144' => 144
		);
	}
}
