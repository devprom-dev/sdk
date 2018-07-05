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

        $versionField = $page_it->getId() == $object_it->get('TargetPage') ? 'TargetDocumentVersion' : 'SourceDocumentVersion';
        if ( $object_it->get($versionField) != '' ) {
            $title .= '['.$object_it->get($versionField).'] ';
        }

	 	$caption = $page_it->getDisplayName();

	 	$document_name_field = $page_it->getId() == $object_it->get('TargetPage') ? 'TargetDocumentName' : 'SourceDocumentName';
	 	if ( !in_array($object_it->get($document_name_field), array('', $page_it->get('Caption'))) ) {
	 		$title .= $object_it->get($document_name_field).' / ';
	 	}
	 	$title .= $caption.' ('.$page_it->get('StateName').')';

	 	return $title;
	}
}