<?php

include "TransitionRoleIterator.php";
include "predicates/TransitionRolePredicate.php";

class TransitionRole extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('pm_TransitionRole');
 	}
 	
 	function createIterator() 
 	{
 		return new TransitionRoleIterator( $this );
 	}
}
