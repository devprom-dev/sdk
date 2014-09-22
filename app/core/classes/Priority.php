<?php

include "PriorityIterator.php";

class Priority extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('Priority');
 		
 		$this->setSortDefault( new SortOrderedClause() );
 		
 		$this->setAttributeDescription( 'RelatedColor', text(1853) );
 	}
 	
	function createIterator() 
	{
		return new PriorityIterator($this);
	}
}
