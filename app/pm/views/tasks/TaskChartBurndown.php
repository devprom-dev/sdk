<?php

class TaskChartBurndown extends TaskChart
{
	function getChartWidget()
	{
		$iteration = getFactory()->getObject('Iteration');
		
		$values = $this->getFilterValues();
		if ( in_array(trim($values['iteration']), array('', 'all', 'hide', 'none')) ) {
			$iteration_it = $iteration->getRegistry()->Query(
					array (
                        new IterationTimelinePredicate(IterationTimelinePredicate::CURRENT),
                        new FilterVpdPredicate()
					)
				);
		}
		else {
			$iteration_it = $iteration->getExact($values['iteration']);
		}
		
		$flot = new FlotChartBurndownWidget();
		$flot->showPoints( false );
		$flot->setUrl( getSession()->getApplicationUrl().'chartburndown.php?release_id='.$iteration_it->getId().'&json=1' );

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