<?php

class StateBusinessActionIterator extends OrderedIterator
{
	function apply( & $object_it ) {
	    if ( !is_object($this->get('RuleObject')) ) return true;
		return $this->get('RuleObject')->apply( $object_it );
	}

	function getRule() {
		return $this->get('RuleObject');
	}

	function checkType( $class_name ) {
		return is_a($this->get('RuleObject'), $class_name);
	}
}