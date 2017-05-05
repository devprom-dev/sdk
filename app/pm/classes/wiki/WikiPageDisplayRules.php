<?php

class WikiPageDisplayRules
{
	static public function getTraceDisplayName( $object_it, $page_it )
	{
        $title = '';

        if ( $page_it->get('BrokenTraces') != '' ) {
            $title .= '<img class="trace-state" id="trace-state-'.$page_it->getId().'" src="/images/exclamation.png" title="'.text(770).'"> ';
        }

		if ( $object_it->object->IsReference('Type') && $object_it->get('Type') != '' ) {
	 		$title .= $object_it->getRef('Type')->getDisplayName().": ";
		}

	 	$uid = new ObjectUID($object_it->get($object_it->object->getBaselineReference()));
	 	$title .= $uid->getUidIcon( $page_it ).' ';
	 	
	 	$caption = $page_it->getDisplayName();

	 	$document_name_field = $page_it->getId() == $object_it->get('TargetPage') ? 'TargetDocumentName' : 'SourceDocumentName';
	 	if ( !in_array($object_it->get($document_name_field), array('', $page_it->get('Caption'))) ) {
	 		$title .= $object_it->get($document_name_field).' / ';
	 	}
	 	$title .= $caption.' ('.$page_it->get('StateName').')';

		if ( $object_it->get($object_it->object->getBaselineReference()) == '' ) return $title;
		$title .= ' ['.$object_it->getRef($object_it->object->getBaselineReference())->getDisplayName().']';

	 	return $title;
	}
}