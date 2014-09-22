<?php

include "RequestTypeIterator.php";

class RequestType extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('pm_IssueType');
 		
 		$this->setAttributeDescription( 'RelatedColor', text(1852) );
 	}
 	
	function createIterator()
	{
	    return new RequestTypeIterator( $this );
	}
	
	function getPage()
	{
	    return getSession()->getApplicationUrl($this).'project/dicts/RequestType?';
	}
}
