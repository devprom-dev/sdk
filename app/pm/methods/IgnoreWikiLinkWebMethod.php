<?php

include_once "ActuateWikiLinkWebMethod.php";

class IgnoreWikiLinkWebMethod extends ActuateWikiLinkWebMethod
{
	function getCaption() 
	{
		$page_it = $this->getObjectIt()->getRef('TargetPage');
		
		$title = $page_it->get('DocumentVersion') != '' 
				? $page_it->get('DocumentVersion') : $this->getObjectIt()->get('TargetDocumentName');
		
		return str_replace('%1', $title, text(1724));
	}
}