<?php

class StateBusinessActionIterator extends CacheableIterator
{
	function apply( $object_it, $data ) {
		$rule = $this->getRule();
	    if ( !is_object($rule) ) return true;
        $rule->setData( $data );
		return $rule->apply( $object_it );
	}

	function getRule() {
		return $this->get('RuleObject');
	}

	function checkType( $class_name ) {
		return is_a($this->getRule(), $class_name);
	}
}