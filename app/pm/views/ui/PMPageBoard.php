<?php

class PMPageBoard extends PageBoard
{
    function PMPageBoard( $object )
    {
        parent::PageBoard( $object );
    }
    
    function getPredicates( $filters )
    {
    	$predicates = parent::getPredicates( $filters );
    	
    	if ( !$this->hasCommonStates() && !$this->getTable()->hasCrossProjectFilter() )
		{
			$predicates[] = new FilterBaseVpdPredicate();
		}
		
    	return $predicates;
    }
    
	function getGroupFields()
	{
	    return array_diff(parent::getGroupFields(), $this->getObject()->getAttributesByGroup('trace')); 
	}
	
	function hasCommonStates()
	{
 		$classname = $this->getBoardAttributeClassName();

 		if ( $classname == '' ) return false;
 		
 		$value_it = getFactory()->getObject($classname)->getRegistry()->Query(
 				array (
 						new FilterVpdPredicate()
 				)
 		);
 		
 		$values = array();
 		
 		while( !$value_it->end() )
 		{
 			$values[$value_it->get('VPD')][] = $value_it->get('Caption');
 			$value_it->moveNext();
 		}
 		
 		$example = array_shift($values);
 		
 		foreach( $values as $attributes )
 		{
 			if ( count(array_diff($example, $attributes)) > 0 || count(array_diff($attributes, $example)) > 0 ) return false;
 		}
 		
 		return true;
	}
}
