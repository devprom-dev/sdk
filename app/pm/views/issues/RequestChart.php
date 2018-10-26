<?php

class RequestChart extends PMPageChart
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
 	
 	function getGroupDefault()
	{
		return 'State';
	}
	
	function getGroupFields() 
	{
		return array_merge( parent::getGroupFields(), array( 'ClosedInVersion', 'SubmittedVersion' ) );
	}
}
