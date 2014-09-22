<?php

class StateBusinessActionIterator extends OrderedIterator
{
	function apply( & $object_it )
	{
	    if ( !is_object($this->get('RuleObject')) ) return true;
	    
		return $this->get('RuleObject')->apply( $object_it );
	}
}