<?php

class StateBaseIterator extends OrderedIterator
{
	function getNeighbors()
	{
		$sql = " SELECT t.* " .
			   "   FROM pm_State t" .
			   "  WHERE t.pm_StateId <> ".$this->getId().
			   $this->object->getFilterPredicate().
			   "  ORDER BY Caption ASC ";
			   
		return $this->object->createSQLIterator($sql);
	}
	
	function getObject()
	{
		return getFactory()->getObject( $this->get('ObjectClass') );
	}

	function getObjectsCount()
	{
		$object = $this->getObject();
		$object->addFilter( new ObjectStatePredicate($this) );
		$object->addFilter( new FilterBaseVpdPredicate() );
		
		$count_aggregate = new AggregateBase( 'State' );
		$object->addAggregate( $count_aggregate );
		$it = $object->getAggregated();
		$cnt = $it->get( $count_aggregate->getAggregateAlias() );
		
		return $cnt == '' ? 0 : $cnt;
	}

	function getTransitionIt()
	{
		if ( $this->getId() == '' ) return getFactory()->getObject('Transition')->getEmptyIterator();
		
		$it = getFactory()->getObject('Transition')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('SourceState', $this->getId())
				)
		);
		$it->object->setStateAttributeType( $this->object );
		
		return $it;
	}
	
	function getTerminal()
	{
	    $ref_names = array();
	    
	    $rowset = $this->getRowset();
    
	    foreach( $rowset as $pos => $row )
	    { 
	        if ( $row['IsTerminal'] == 'Y' ) $ref_names[$row['OrderNum']] = $row['ReferenceName'];  
	    }
	    
		ksort($ref_names);
		$ref_names = array_unique(array_values($ref_names));
	    
	    return $ref_names;
	}
	
 	function getNonTerminal()
	{
	    $ref_names = array();
	    
	    $rowset = $this->getRowset();
	    
	    foreach( $rowset as $pos => $row )
	    { 
	        if ( $row['IsTerminal'] != 'Y' ) $ref_names[$row['OrderNum']] = $row['ReferenceName'];  
	    }
	    
		ksort($ref_names);
		$ref_names = array_unique(array_values($ref_names));
	    
		return $ref_names;
	}
	
	function getWarningMessage( $object_it = null )
	{
		return '';
	}
	
	function getDbSafeReferenceName()
	{
		return preg_replace('/\s+/', '_', $this->get('ReferenceName'));
	}
}