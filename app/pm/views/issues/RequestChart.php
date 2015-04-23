<?php

class RequestChart extends PMPageChart
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
 	
 	function getGroupDefault()
	{
		return 'State';
	}
	
	function getGroupFields() 
	{
		return array_merge( parent::getGroupFields(), array( 'ClosedInVersion', 'SubmittedVersion' ) );
	}
	 		
	function getAggByFields()
	{
		$fields = parent::getAggByFields();
		
		foreach( $fields as $key => $field )
		{
			if ( $field == 'Function' && !getSession()->getProjectIt()->getMethodologyIt()->HasFeatures() )
			{
				unset( $fields[$key] );
			}

			if ( $field == 'Fact' )
			{
				unset( $fields[$key] );
			}
		}
		
		return $fields;
	}
	
	function getChartWidget()
	{
		global $_REQUEST, $project_it, $model_factory;

		$release = $model_factory->getObject('Release');
		
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
		
		switch ( $this->getTable()->getReportBase() )
		{
			case 'releaseburndown':
				$flot = new FlotChartBurndownWidget();
				$flot->setLegend( false );
				$flot->showPoints( false );

				$flot->setUrl(getSession()->getApplicationUrl().
					'chartburndownversion.php?version='.$release_it->getId().'&json=1');
				return $flot;
				
			case 'releaseburnup':
				$flot = new FlotChartBurnupWidget();
				$flot->setLegend( false );
				$flot->showPoints( false );

				$flot->setUrl( getSession()->getApplicationUrl().
					'chartburnup.php?release='.$release_it->getId() );
				
                return $flot;
                
			case 'projectburnup':
				$flot = new FlotChartBurnupWidget();
				$flot->setLegend( false );
				$flot->showPoints( false );

				$flot->setUrl( getSession()->getApplicationUrl().'chartburnupproject.php' );
				return $flot;
				
			default:
				return parent::getChartWidget();
		}
	}
}
