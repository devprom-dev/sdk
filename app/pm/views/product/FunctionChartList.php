<?php

include "FeatureIntervalsFrame.php";

class FunctionChartList extends FunctionList
{
 	function __construct( $object )
 	{
 		parent::__construct( $object );
 	}

 	function retrieve()
 	{
 	    parent::retrieve();

 		$cache = new IntervalCache($this->getIteratorRef());
 		$cache->store( $this->getIteratorRef()->object->getDatesSql() );
 		
 		$this->chart = new FeatureIntervalsFrame();
 		$this->chart->setIntervalIt( $cache->getIterator() );
 		
 		$year = new ViewDateYearWebMethod();
 		$this->chart->setYear( $year->getValue() );
 	}
 	
	function IsNeedToDelete() { return false; }
	function IsNeedToDisplayOperations() { return false; } 
	function IsNeedToSelect() { return false; } 
	function IsNeedToDisplay( $attr ) { return $attr == 'Caption' || $attr == 'ChartColumn'; }

	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'Function' )
			return '45%';
			
		return parent::getColumnWidth( $attr );
	}
	
	function getColumns()
	{
		$this->object->addAttribute('ChartColumn', '', translate('График реализации'), true);
		
		return parent::getColumns();
	}

	function getHeaderAttributes( $attr )
	{
		$info = parent::getHeaderAttributes( $attr );
		
		switch ( $attr )
		{
			case 'ChartColumn':
			    
			    ob_start();
			    
			    $this->chart->drawListHeader();
			    
				$info['name'] = ob_get_contents();

				ob_end_clean();
				
				break;
		}
		
		return $info;
	}

	function drawCell( $object_it, $attr ) 
	{
		global $model_factory;
		
		switch ( $attr )
		{
			case 'ChartColumn':
				$this->chart->draw( $object_it->getId() );
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}
}
