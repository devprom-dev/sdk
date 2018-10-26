<?php

class StateBusinessRuleIterator extends OrderedIterator
{
	function check( $object_it, $transitionIt )
	{
		$rule = $this->getRule();
		if ( !is_object($rule) ) return true;
		return $rule->check( $object_it, $transitionIt );
	}

	function getRule() {
		return $this->get('RuleObject');
	}

	function getNegativeReason()
	{
		$rule = $this->getRule();
		if ( !is_object($rule) ) return '';
		return $rule->getNegativeReason();
	}
}