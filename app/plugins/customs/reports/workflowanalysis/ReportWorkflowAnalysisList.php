<?php

class ReportWorkflowAnalysisList extends PMStaticPageList
{
	private $first_state;
	
	function retrieve()
	{
		parent::retrieve();
		$this->first_state = array_shift($this->getObject()->getNonTerminalStates());
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
			echo getSession()->getLanguage()->getDurationWording($object_it->get($attr), 24);

			if ( $object_it->get('LeadTime') > 0 ) {
                echo ' ('.min(100,round($object_it->get($attr) * 100 / $object_it->get('LeadTime'), 0)).'%)';
            }
		}
		else
		{
			parent::drawCell( $object_it, $attr );
		}
	}
}