<?php
include "persisters/WikiPageChangePersister.php";
include "predicates/WikiPageChangeYounger.php";
include "WikiPageChangeRegistry.php";

class WikiPageChange extends Metaobject
{
	function __construct()
	{
		parent::__construct('WikiPageChange', new WikiPageChangeRegistry($this));
		$this->addPersister( new WikiPageChangePersister() );
	}

    function getReferenceName()
    {
        return '';
    }
}