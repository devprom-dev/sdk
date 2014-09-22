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
 	
 	private $aggregated_state = null;
}