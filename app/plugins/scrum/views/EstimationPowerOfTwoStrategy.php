<?php
include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

class EstimationPowerOfTwoStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text('scrum16');
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
			'0:4' => translate('Простые'),
			'8:32' => translate('Средние'),
			' 64' => translate('Сложные')
		);
	}
	
	function getScale()
	{
		return array (
 			' 0' => 0,
		    ' 1' => 1,
 			' 2' => 2,
 			' 4' => 4,
 			' 8' => 8,
 			' 16' => 16,
 			' 32' => 32,
 			' 64' => 64,
 			' 128' => 128,
 			' 256' => 256
		);
	}
}
