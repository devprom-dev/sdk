<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

include_once "FieldStoryPoints.php";

class EstimationPowerOfTwoStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text('scrum16');
	}
	
	function getEstimationText()
	{
		return text('scrum11');
	}
	
	function getVelocityText()
	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease() )
		{
			return text('scrum12');
		}
		else
		{
			return text('scrum13');
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
	
	function getEstimationFormField( $form )
	{
		return new FieldStoryPoints( $form->getObject(), array (
 			array( 'value' => ' 0', 'caption' => '0' ),
		    array( 'value' => ' 1', 'caption' => '1' ),
 			array( 'value' => ' 2', 'caption' => '2' ),
 			array( 'value' => ' 4', 'caption' => '4' ),
 			array( 'value' => ' 8', 'caption' => '8' ),
 			array( 'value' => ' 16', 'caption' => '16' ),
 			array( 'value' => ' 32', 'caption' => '32' ),
 			array( 'value' => ' 64', 'caption' => '64' ),
 			array( 'value' => ' 128', 'caption' => '128' ),
 			array( 'value' => ' 256', 'caption' => '256' )
		));
	}
}
