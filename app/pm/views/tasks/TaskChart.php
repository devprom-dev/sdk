<?php

class TaskChart extends PMPageChart
{
	function getPredicates( $values )
	{
	    $predicates = parent::getPredicates( $values );

	    if ( $this->getGroup() == 'history' )
	    {
    	    foreach( $predicates as $key => $predicate )
    	    {
    	        if ( is_a($predicate, 'FilterModifiedAfterPredicate') ) unset($predicates[$key]);
    	        if ( is_a($predicate, 'FilterModifiedBeforePredicate') ) unset($predicates[$key]);
    	    }
	    }
	    
		return $predicates;
	}
	
	function getReport()
	{
	    return $_REQUEST['report'];
	}
	
	function getChartWidget()
	{
		if ( $this->getTable()->getReportBase() != 'iterationburndown' ) return parent::getChartWidget();
		
		$iteration = getFactory()->getObject('Iteration');
		
		$values = $this->getFilterValues();
		if ( in_array(trim($values['release']), array('', 'all', 'hide', 'none')) )
		{
			$iteration_it = $iteration->getRegistry()->Query(
					array (
							new IterationTimelinePredicate(IterationTimelinePredicate::CURRENT),
							new FilterVpdPredicate()
					)
				);
		}
		else
		{
			$iteration_it = $iteration->getExact($values['release']);
		}
		
		$flot = new FlotChartBurndownWidget();
		$flot->setLegend( false );
		$flot->showPoints( false );
		$flot->setUrl( getSession()->getApplicationUrl().
			'chartburndown.php?release_id='.$iteration_it->getId().'&json=1' );
	
		return $flot;
	}
}