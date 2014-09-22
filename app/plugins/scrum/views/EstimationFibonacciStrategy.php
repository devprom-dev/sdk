<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

include_once "FieldStoryPoints.php";

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
	
	function getEstimationFormField( $form )
	{
		return new FieldStoryPoints( $form->getObject(), array (
 			array( 'value' => ' 0', 'caption' => '0' ),
		    array( 'value' => ' 1', 'caption' => '1' ),
 			array( 'value' => ' 2', 'caption' => '2' ),
 			array( 'value' => ' 3', 'caption' => '3' ),
 			array( 'value' => ' 5', 'caption' => '5' ),
 			array( 'value' => ' 8', 'caption' => '8' ),
 			array( 'value' => ' 13', 'caption' => '13' ),
 			array( 'value' => ' 21', 'caption' => '21' ),
 			array( 'value' => ' 34', 'caption' => '34' ),
 			array( 'value' => ' 55', 'caption' => '55' ),
		    array( 'value' => ' 89', 'caption' => '89' ),
		    array( 'value' => ' 144', 'caption' => '144' )
		));
	}
}
