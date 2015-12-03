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

	function getDefaultAttributeValue( $attr )
	{
		switch ( $attr )
		{
			case 'ReferenceName':
				return uniqid('IssueType_');
			default:
				return parent::getDefaultAttributeValue( $attr );
		}
	}

	function getPage()
	{
	    return getSession()->getApplicationUrl($this).'project/dicts/RequestType?';
	}
}
