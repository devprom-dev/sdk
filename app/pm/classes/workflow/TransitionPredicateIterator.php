<?php

class TransitionPredicateIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$ref_it = $this->getRef('Predicate');
 		
 		return $ref_it->getId() != '' 
 				? $ref_it->getDisplayName() 
 				: preg_replace('/%1/', $this->get('Predicate'), text(1881));
 	}
}
