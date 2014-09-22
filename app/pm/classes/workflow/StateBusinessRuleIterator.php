<?php

class StateBusinessRuleIterator extends OrderedIterator
{
	function check( & $object_it )
	{
	    if ( !is_object($this->get('RuleObject')) ) return true;
	    
		return $this->get('RuleObject')->check( $object_it );
	}
    
	function getNegativeReason()
	{
	    if ( !is_object($this->get('RuleObject')) ) return '';
	    
	    return $this->get('RuleObject')->getNegativeReason();
	}
}