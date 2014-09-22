<?php

class TransitionPredicateIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$predicate_it = $this->getRef('Predicate');
 			
 		return $predicate_it->getDisplayName();
 	}
}
