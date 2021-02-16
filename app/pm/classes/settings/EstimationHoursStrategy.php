<?php
include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

class EstimationHoursStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text(1102);
	}
	
	function getDimensionText( $value )
	{
		return str_replace("%1", $value, text(1120));
	}
	
	function hasEstimationValue()
	{
		return true;
	}
	
	function getScale()
	{
		$values = array (
			'0', 0.5, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 16, 25, 40
		);
		$scale = array();
		foreach( $values as $value ) {
			$scale[' '.$value] = $value;
		}
		return $scale;
	}

	function hasDiscreteValues() {
		return false;
	}
}
