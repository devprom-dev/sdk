<?php

class StateSharedVpdPredicate extends FilterPredicate
{
 	var $shared;
 	
 	function StateSharedVpdPredicate ( $object )
 	{
 		parent::__construct( 'vpd' );
 		$this->shared = $object;
 	}
 	
 	function _predicate( $filter )
 	{
		return $this->shared->getVpdPredicate();
 	}
} 
