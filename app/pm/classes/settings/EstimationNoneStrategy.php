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
		if ( !is_object($object) ) {
		    $object = getFactory()->getObject('pm_ChangeRequest');
        }

		$sum_aggregate = new AggregateBase( $group, $object->getIdAttribute(), $this->getEstimationAggregate() );
		$object->addAggregate( $sum_aggregate );
		$request_it = $object->getAggregated();

		$data = array();
		while ( !$request_it->end() )
		{
		    $data[$request_it->get( $sum_aggregate->getAttribute() )] =
                $request_it->get( $sum_aggregate->getAggregateAlias() );
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
	
	function getDimensionText( $value )
	{
		return str_replace("%1", $value, text(1118));
	}
	
	function hasEstimationValue() {
		return false;
	}

    function hasDiscreteValues() {
        return false;
    }
}
