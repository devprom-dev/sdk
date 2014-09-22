<?php

include_once "BusinessRulePredicate.php";

class IssueStateNonBlockedRule extends BusinessRulePredicate
{
 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1142);
 	}
 	
 	function check( $object_it )
 	{
 		return !$object_it->IsBlocked();
 	}
 	
 	function getNegativeReason()
 	{
 		return text(961);
 	}
}
