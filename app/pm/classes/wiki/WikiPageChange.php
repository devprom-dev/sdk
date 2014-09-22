<?php

include "persisters/WikiPageChangePersister.php";

class WikiPageChange extends Metaobject
{
	function __construct()
	{
		parent::__construct('WikiPageChange');
		
		$this->addPersister( new WikiPageChangePersister() );
	}
}