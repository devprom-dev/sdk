<?php

include_once "BusinessRulePredicate.php";

class IssueBlockedWithinImplementation extends BusinessRulePredicate
{
	private $object = null;
	private $terminals = array();
	
	function __construct()
	{
		$this->object = getFactory()->getObject('pm_ChangeRequest');
		$this->terminals = $this->object->getTerminalStates(); 
	}
	
 	function getObject()
 	{
 		return $this->object;
 	}
 	
 	function getDisplayName()
 	{
 		return text(2042);
 	}
 	
 	function check( $object_it )
 	{
 		foreach(preg_split('/,/', $object_it->get('LinksWithTypes')) as $link_info)
		{
			list($type_name, $link_id, $type_ref, $link_state, $direction) = preg_split('/:/',$link_info);
			if ( $type_ref == 'implemented' && $direction == 2 && !in_array($link_state,$this->terminals)) return false;
		}
 		return true;
 	}
 	
 	function getNegativeReason()
 	{
 		return text(2041);
 	}
}
