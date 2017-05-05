<?php

include "FlotChartVelocityWidget.php";

class VelocityChart extends PMPageChart
{
 	function __construct( $object )
 	{
		parent::__construct( $object );
 	}

	function _getGroupFields() 
	{
		return array();
	}
	 		
	function _getAggByFields()
	{
		return array();
	}
	
	function getChartWidget()
	{
		return new FlotChartVelocityWidget();
	}

    protected function getDemoData($aggs)
    {
        $x_attribute = $aggs[0]->getAttribute();
        $y_attribute = $aggs[0]->getAggregateAlias();
        return array(
            array(
                $x_attribute => '0',
                $y_attribute => 5
            ),
            array(
                $x_attribute => '1',
                $y_attribute => 7
            ),
            array(
                $x_attribute => '2',
                $y_attribute => 12
            )
        );
    }
}