<?php

class TaskChart extends PMPageChart
{
	function getPredicates( $values )
	{
	    $predicates = parent::getPredicates( $values );

	    if ( $this->getGroup() == 'history' )
	    {
    	    foreach( $predicates as $key => $predicate )
    	    {
    	        if ( is_a($predicate, 'FilterModifiedAfterPredicate') ) unset($predicates[$key]);
    	        if ( is_a($predicate, 'FilterModifiedBeforePredicate') ) unset($predicates[$key]);
    	    }
	    }
	    
		return $predicates;
	}
}