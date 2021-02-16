<?php

class RequestReleaseBurndownChart extends RequestChart
{
	function getChartWidget()
	{
		$release = getFactory()->getObject('Release');
		
		$values = $this->getFilterValues();
		if ( in_array($values['release'], array('', 'all', 'hide')) )
		{
			$release->addFilter( new ReleaseTimelinePredicate('current') );
			$release_it = $release->getAll();
			
			$values['release'] = $release_it->getId(); 
		}
		else
		{
			$release_it = $release->getExact($values['release']);
		}
		
        $flot = new FlotChartBurndownWidget();
        $flot->showPoints( false );
        $flot->setUrl(getSession()->getApplicationUrl().
            'chartburndownversion.php?version='.$release_it->getId().'&json=1');
        return $flot;
	}

    function getDemo()
    {
        return false;
    }

    function getAggByFields()
    {
        return array();
    }

    function getAggregateBy()
    {
        return array();
    }

    function getAggregates()
    {
        return array();
    }

    function getAggregators()
    {
        return array();
    }

    function getLegendVisible()
    {
        return true;
    }

    function getOptions($filter_values)
    {
        return array();
    }

    function getGroupFields()
    {
        return array();
    }
}
