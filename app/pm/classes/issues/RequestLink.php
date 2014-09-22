<?php

include "RequestLinkIterator.php";
include "predicates/RequestLinkedFilter.php";

class RequestLink extends Metaobject
{
 	function RequestLink() 
 	{
 		parent::Metaobject('pm_ChangeRequestLink');
 	}
 	
 	function createIterator() 
 	{
 		return new RequestLinkIterator( $this );
 	}
}
