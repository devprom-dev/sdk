<?php

include "StateMetaRegistry.php";

class StateMeta extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_State', new StateMetaRegistry());
 	}
 	
 	public function setAggregatedStateObject( $object )
 	{
 		$this->aggregated_state = $object;
 	}
 	
 	public function getAggregatedStateObject()
 	{
 		return $this->aggregated_state;
 	}
	
	public function setStatesDelimiter( $delimiter )
	{
		$this->states_delimiter = $delimiter;
	}
	
	public function getStatesDelimiter()
	{
		return $this->states_delimiter;
	}
 	
	private $states_delimiter = ",";
 	private $aggregated_state = null;
}