<?php

class RequestProjectBurnUpChart extends RequestChart
{
	function getChartWidget()
	{
        $flot = new FlotChartBurnupWidget();
        $flot->setLegend( false );
        $flot->showPoints( false );
        $flot->setUrl( getSession()->getApplicationUrl().'chartburnup.php' );
        return $flot;
	}

	function getDemo()
    {
        return false;
    }

    function getOptions($filter_values)
    {
        return array();
    }

    function getActions($object_it)
    {
        return array();
    }

    function getExportActions()
    {
        return array();
    }

    function getAggByFields()
    {
        return array();
    }

    function getGroupFields()
    {
        return array();
    }

    function getAggregators()
    {
        return array();
    }
}
