<?php

include "UpdateIterator.php";

class Update extends MetaobjectCacheable
{
	function __construct ()
	{
		parent::__construct('cms_Update');
		
		$this->setSortDefault( new SortRecentClause() );
	}
	
	function createIterator()
	{
		return new UpdateIterator( $this );
	}
}
