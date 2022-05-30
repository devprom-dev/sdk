<?php
include "IssueAuthorRegistry.php";
include "IssueAuthorIterator.php";

class IssueAuthor extends Metaobject
{
 	function __construct( $registry = null )
 	{
 		parent::__construct('cms_User', is_object($registry) ? $registry : new IssueAuthorRegistry());
		$this->addAttributeGroup('Email', 'alternative-key');
        $this->addAttributeGroup('Caption', 'search-attributes');
        $this->addAttributeGroup('Email', 'search-attributes');
 	}

    function createIterator() {
        return new IssueAuthorIterator($this);
    }

    function getDisplayName() {
        return text(2841);
    }
}