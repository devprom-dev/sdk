<?php
 
abstract class FlotChartWidget
{
 	var $legend, $points, $data;
	private $colors = array();
 	
 	function __construct()
 	{
 		$this->legend = true;
 		$this->points = true;
 		$this->data = array();
 	}
 	
 	function setLegend( $show )
 	{
 		$this->legend = $show;
 	}

 	function getShowLegend()
 	{
 	    return $this->legend;
 	}
 	
	function showPoints( $points )
	{
		$this->points = $points;
	}

	function getShowPoints()
	{
	    return $this->points;
	}

	function setData( $data )
	{
	    $this->data = $data;
	}
	
 	function getData()
 	{
 	    return $this->data;
 	}

	function setColors( $colors ) {
		$this->colors = $colors;
	}

	function getColors() {
		return array_filter( $this->colors,
            function($value) {
                return strpos($value, '#') !== false || strpos($value, 'rgb') !== false;
            }
        );
	}

	function getStyle() {
        return "";
    }

 	abstract public function draw( $chart_id );
}