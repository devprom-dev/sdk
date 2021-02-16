<?php
include "IssueAuthorRegistry.php";

class IssueAuthor extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('cms_User', new IssueAuthorRegistry());
		$this->addAttributeGroup('Email', 'alternative-key');
        $this->addAttributeGroup('Caption', 'search-attributes');
        $this->addAttributeGroup('Email', 'search-attributes');
 	}

 	function getDisplayName() {
        return text(2841);
    }
}