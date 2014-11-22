<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategy.php";

class EstimationNoneStrategy extends EstimationStrategy
{
	function getDisplayName()
	{
		return text(1097);
	}
	
	function getEstimationAggregate()
	{
	    return 'COUNT';
	}
	
	function getEstimation( $object = null, $estimation = 'Estimation', $group = 'Project' )
	{
		global $model_factory;
		
		if ( !is_object($object) ) 
			$object = $model_factory->getObject('pm_ChangeRequest'); 
			
		$sum_aggregate = new AggregateBase( $group, $estimation, $this->getEstimationAggregate() );
		
		$object->addAggregate( $sum_aggregate );
		
		$request_it = $object->getAggregated();
		
		$data = array();
		
		while ( !$request_it->end() )
		{
		    $data[$request_it->get( $sum_aggregate->getAttribute() )] = $request_it->get( $sum_aggregate->getAggregateAlias() );
		     
		    $request_it->moveNext();
		}
		
		return $data;
	}
	
	function getEstimationByIt( $request_it )
	{
 	 	$total_open = 0;
		
 	 	$request_it->moveFirst();
		
		while( !$request_it->end() )
		{
			if ( !$request_it->IsFinished() ) $total_open += 1;
			
			$request_it->moveNext();
		}

		return array($total_open, 100);
	}
	
	function getEstimationText()
	{
		return text(1098);
	}
	
	function getVelocityText( $object )
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if ( (!$methodology_it->HasPlanning() || $object instanceof Iteration) && $methodology_it->HasFixedRelease() )
		{
			return text(1118);
		}
		else
		{
			return text(1100);
		}
	}
	
	function getDimensionText( $value )
	{
		return str_replace("%1", $value, text(1118));
	}
	
	function hasEstimationValue()
	{
		return false;
	}
}
