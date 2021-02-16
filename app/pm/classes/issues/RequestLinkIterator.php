<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateDurationPersister.php";

class RequestLinkIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
		$uid = new ObjectUID;
		
		$link_type_it = $this->getRef('LinkType');
		
		$target_it = $this->object->getAttributeObject('TargetRequest')->getRegistry()->Query(
			array (
                new FilterInPredicate($this->get('TargetRequest')),
                new StateDurationPersister()
			)
		);
		$target_it = $target_it->getSpecifiedIt();
        $targetStateIt = $target_it->getStateIt();

		$title = $uid->getUidIconGlobal($target_it).' '.$target_it->getDisplayName().' ('.$targetStateIt->get('Caption').')';
		if ( $link_type_it->getDisplayName() != '' ) {
            $title = $link_type_it->getDisplayName().': ' . $title;
        }
        return $title;
 	}
 	
 	function getTraceDisplayName() 
 	{
		$uid = new ObjectUID;

		$link_type_it = $this->getRef('LinkType');
		$target_it = $this->getRef('TargetRequest')->getSpecifiedIt();

		$title = $uid->getObjectUid($target_it).' '.$target_it->getDisplayName();
        if ( $link_type_it->getDisplayName() != '' ) {
            $title = $link_type_it->getDisplayName().': ' . $title;
        }

		return $title;
 	}
}