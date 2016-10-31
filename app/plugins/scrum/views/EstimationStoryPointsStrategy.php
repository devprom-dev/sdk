<?php
include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

class EstimationStoryPointsStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text('scrum10');
	}
	
	function getEstimationText()
	{
		return text('scrum11');
	}
	
	function getVelocityText($object)
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if ( (!$methodology_it->HasPlanning() || $object instanceof Iteration) && $methodology_it->HasFixedRelease() )
		{
			return text('scrum13');
		}
		else
		{
			return text('scrum12');
		}
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
			'5:13' => translate('Средние'),
			' 20' => translate('Сложные')
		);
	}
	
	function getScale()
	{
		return array (
 			' 0' => 0,
 			' 0.5' => 0.5,
 			' 1' => 1,
 			' 2' => 2,
 			' 3' => 3,
 			' 5' => 5,
 			' 8' => 8,
 			' 13' => 13,
 			' 20' => 20,
 			' 40' => 40,
 			' 100' => 100,
 		);
	}
}
