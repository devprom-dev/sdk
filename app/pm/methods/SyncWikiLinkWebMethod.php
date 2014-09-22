<?php

include_once "ActuateWikiLinkWebMethod.php";

class SyncWikiLinkWebMethod extends ActuateWikiLinkWebMethod
{
	function getCaption() 
	{
		$page_it = $this->getObjectIt()->getRef('SourcePage');
		
		$title = $page_it->get('DocumentVersion') != '' 
				? $page_it->get('DocumentVersion') : $this->getObjectIt()->get('SourceDocumentName');
		
		return str_replace('%1', $title, text(1564));
	}
	
	function execute_request()
 	{
 		parent::execute_request();
 		
		$link_it = $this->getObjectIt();
		
		$baseline_it = $this->getBaselineIt();
		
		if ( is_object($baseline_it) )
		{
			$page_it = $link_it->object->getAttributeObject('SourcePage')->getRegistry()->Query( array (
					new FilterInPredicate($link_it->get('SourcePage')),
					new SnapshotItemValuePersister($baseline_it->getId()) 
			)); 
		}
		else
		{
			$page_it = $link_it->getRef('SourcePage');
		}
			
		if ( $page_it->getId() < 1 ) throw new Exception('Unable get source page of the trace');
		
		$link_it->getRef('TargetPage')->modify( 
				array (
						'Caption' => $page_it->getHtmlDecoded('Caption'),
						'Content' => $page_it->getHtmlDecoded('Content')
				)
		);
 	}
}