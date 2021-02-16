<?php

include "ImportanceIterator.php";

class Importance extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('pm_Importance');
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
 		$this->setSortDefault( new SortOrderedClause() );
 	}

	function createIterator() 
	{
		return new ImportanceIterator($this);
	}
}
