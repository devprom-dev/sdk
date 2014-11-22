<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

include_once "FieldStoryPoints.php";

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
	
	function getEstimationFilter()
	{
		return new ViewRequestEstimationWebMethod();
	}
	
	function getEstimationPredicate( $value )
	{
		return new RequestEstimationFilter( $value );
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
	
	function getEstimationFormField( $form )
	{
		return new FieldStoryPoints( $form->getObject(), array (
 			array( 'value' => ' 0', 'caption' => '0' ),
 			array( 'value' => ' 0.5', 'caption' => '0.5' ),
 			array( 'value' => ' 1', 'caption' => '1' ),
 			array( 'value' => ' 2', 'caption' => '2' ),
 			array( 'value' => ' 3', 'caption' => '3' ),
 			array( 'value' => ' 5', 'caption' => '5' ),
 			array( 'value' => ' 8', 'caption' => '8' ),
 			array( 'value' => ' 13', 'caption' => '13' ),
 			array( 'value' => ' 20', 'caption' => '20' ),
 			array( 'value' => ' 40', 'caption' => '40' ),
 			array( 'value' => ' 100', 'caption' => '100' )
		));
	}
}
