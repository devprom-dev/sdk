<?php

class SubversionAuthorRegistry extends ObjectRegistrySQL
{
	function createSQLIterator()
	{
		$sum_aggregate = new AggregateBase( 'Author', 'Author', 'COUNT' );
		
		$object = getFactory()->getObject('SubversionRevision');
		
		$object->addAggregate( $sum_aggregate );
		
		$agg_it = $object->getAggregated();
		
		$data = array();
		
		while( !$agg_it->end() )
		{
			$data[] = array (
					'entityId' => $agg_it->getPos(),
					'ReferenceName' => $agg_it->get($sum_aggregate->getAttribute()),
					'Caption' => $agg_it->get($sum_aggregate->getAttribute())
			);
			
			$agg_it->moveNext();
		}
		
		return $this->createIterator($data);
	}
}