<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

class EstimationHoursStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text(1102);
	}
	
	function getEstimationText()
	{
		return text(1104);
	}
	
	function getVelocityText()
	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease() )
		{
			return text(1105);
		}
		else
		{
			return text(1115);
		}
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
		return array (
				'0', 0.5, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 14, 16, 18, 20, 25, 30, 35, 40, 45, 50 
		);
	}
}
