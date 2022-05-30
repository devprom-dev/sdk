<?php
include_once "BusinessRulePredicate.php";

class IssueBlockedWithinImplementation extends BusinessRulePredicate
{
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(2042);
 	}
 	
 	function check( $object_it, $transitionIt )
 	{
		foreach(preg_split('/,/', $object_it->get('LinksWithTypes')) as $link_info)
		{
			list($type_name, $link_id, $type_ref, $link_state, $direction) = preg_split('/:/',$link_info);
			if ( $type_ref == 'implemented' && $link_state != 'Y' ) return false;
		}
 		return true;
 	}
 	
 	function getNegativeReason() {
 		return text(2041);
 	}
}
