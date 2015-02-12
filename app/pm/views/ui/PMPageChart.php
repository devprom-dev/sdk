<?php

class PMPageChart extends PageChart
{
    function getChartWidget()
    {
	    $report_it = getFactory()->getObject('PMReport')->getExact($this->getTable()->getReport());
	    
	    if ( $report_it->get('WidgetClass') == '' ) return parent::getChartWidget();
	    
	    if ( !class_exists($report_it->get('WidgetClass')) )
	    {
	        throw new Exception('Unknown chart widget class name: '.$report_it->get('WidgetClass'));
	    }
	    
	    $className = $report_it->get('WidgetClass');
	    
	    return new $className; 
    }
}