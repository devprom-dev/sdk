<?php

class ReportWorkflowAnalysisList extends PMStaticPageList
{
	private $first_state;
	
	private $time_scale;
	
	function retrieve()
	{
		parent::retrieve();
		
		$this->first_state = array_shift($this->getObject()->getNonTerminalStates());
		
		$filter_values = $this->getFilterValues();
		
		$this->time_scale = $filter_values['timescale']; 
	}
	
	function buildFilterActions( & $base_actions )
	{
	    parent::buildFilterActions( $base_actions );

	    $this->buildFilterColumnsGroup( $base_actions, 'states' );
	}
	
	function drawCell( $object_it, $attr ) 
	{
		$matches = array();

		if ( preg_match('/State_(\w+)/', $attr, $matches) )
		{
			if ( $matches[1] != $this->first_state && $object_it->get($attr) == 0 ) return;
			
			if ( $this->time_scale == 'days' )
			{
				echo round($object_it->get($attr) / 24, 0);
			}
			else
			{
				echo $object_it->get($attr);
			}
		}
		else
		{
			parent::drawCell( $object_it, $attr );
		}
	}
}