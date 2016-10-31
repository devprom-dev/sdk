<?php
include "SearchResultIterator.php";
include "SearchResultRegistry.php";

class SearchResult extends Metaobject
{
 	function __construct() {
 	    parent::__construct('entity', new SearchResultRegistry($this));
        $this->addAttribute('UID', 'VARCHAR', 'UID', true, false, '', 1);
        $this->setAttributeCaption('Caption', translate('Артефакт'));
        $this->setAttributeCaption('ReferenceName', translate('Артефакт'));
 	}
 	
 	function createIterator() {
 		return new SearchResultIterator( $this );
 	}
}
