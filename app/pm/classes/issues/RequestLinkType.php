<?php

include "RequestLinkTypeIterator.php";

class RequestLinkType extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_ChangeRequestLinkType');
 		
 		$this->setSortDefault( new SortOrderedClause() );
 	}
 	
	function createIterator() 
	{
		return new RequestLinkTypeIterator($this);
	}
}
