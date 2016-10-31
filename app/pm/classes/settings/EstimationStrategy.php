<?php

abstract class EstimationStrategy
{
	abstract function getDisplayName();
	
	function getEstimationAggregate()
	{
	    return 'SUM';
	}
	
	function getEstimation( $object = null, $estimation = 'Estimation', $group = 'Project' )
	{
		global $model_factory;
		
		if ( !is_object($object) ) $object = $model_factory->getObject('pm_ChangeRequest'); 
			
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
 	 	$estimated = 0;
 	 	$nonestimated = array();
		
 	 	$request_it->moveFirst();
 	 	
		while( !$request_it->end() )
		{
			if ( $request_it->get("Estimation") != '' )
			{
				$estimated++;
			}
			else
			{
				array_push($nonestimated, $request_it->getId());
			}
			
			if ( !$request_it->IsFinished() )
			{
				$total_open += $request_it->get("Estimation");
			}

			$request_it->moveNext();
		}

		return array(round($total_open, 1), round($estimated/$request_it->count()*100, 1));
	}
	
	function getEstimationText()
	{
		return '';
	}

	function getVelocityText( $object )
	{
		return '';
	}
	
	function getDimensionText( $text )
	{
		return $text;
	}
	
	function hasEstimationValue()
	{
		return false;
	}
	
	function getEstimationFilter()
	{
		return null;
	}
	
	function getEstimationPredicate( $value )
	{
		return null;
	}
	
	function getScale()
	{
		return array();
	}

	function getFilterScale()
	{
		return array();
	}

	function hasDiscreteValues() {
		return true;
	}
}
