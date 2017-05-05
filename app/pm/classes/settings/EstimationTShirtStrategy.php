<?php
include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

class EstimationTShirtStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text(2200);
	}

	function getDimensionText( $value )
	{
		if ( $value == '' ) return text(2299);
		if ( !is_numeric($value) ) return str_replace("%1", $value, text(2207));
		$label = '';
		$xSizes = floor($value / 8);
		$value -= $xSizes * 8;
		if ( $xSizes > 0 ) $label .= $xSizes.'X ';
		$lSizes = floor($value / 4);
		$value -= $lSizes * 4;
		if ( $lSizes > 0 ) $label .= $lSizes.'L ';
		$mSizes = floor($value / 2);
		$value -= $mSizes * 2;
		if ( $mSizes > 0 ) $label .= $mSizes.'M ';
		if ( $value > 0 ) $label .= $value.'S ';
		return trim($label);
	}
	
	function hasEstimationValue()
	{
		return true;
	}

	function getFilterScale()
	{
		return array(
			' 7' => 'X',
			'4:7' => 'L',
			'2:3' => 'M',
			'0:1' => 'S'
		);
	}
	
	function getScale()
	{
		return array (
 			' X' => 8,
		    ' L' => 4,
 			' M' => 2,
 			' S' => 1
		);
	}

    function getVelocityText($object)
    {
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( $methodology_it->HasFixedRelease() ) {
            return text(2292);
        }
        else {
            return text(2291);
        }
    }
}
