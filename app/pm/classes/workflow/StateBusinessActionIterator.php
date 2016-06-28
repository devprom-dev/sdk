<?php

class StateBusinessActionIterator extends OrderedIterator
{
	function apply( & $object_it ) {
		$rule = $this->getRule();
	    if ( !is_object($rule) ) return true;
		return $rule->apply( $object_it );
	}

	function getRule() {
		return $this->get('RuleObject');
	}

	function checkType( $class_name ) {
		return is_a($this->getRule(), $class_name);
	}
}