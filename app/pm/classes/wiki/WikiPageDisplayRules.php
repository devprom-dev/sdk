<?php

class WikiPageDisplayRules
{
	static public function getTraceDisplayName( $object_it, $page_it )
	{
		if ( $object_it->object->IsReference('Type') && $object_it->get('Type') != '' )
		{
	 		$title = $object_it->getRef('Type')->getDisplayName().": ";
		}
		else
		{
			$title = "";
		}
 		
	 	$uid = new ObjectUID($object_it->get('Baseline'));
	 	
	 	$title .= $uid->getUidIcon( $page_it ).' ';
	 	
	 	$caption = $page_it->getDisplayName();
	 	
	 	$document_name_field = $page_it->getId() == $object_it->get('TargetPage') ? 'TargetDocumentName' : 'SourceDocumentName'; 
	 	
	 	if ( !in_array($object_it->get($document_name_field), array('', $page_it->get('Caption'))) )
	 	{
	 		$title .= $object_it->get($document_name_field).' / ';
	 	}
	 	
	 	$title .= $caption;
	 	
	 	return $title;
	}
}