<?php

include "WikiTypeBaseIterator.php";
include "WikiTypeBaseRegistry.php";

class WikiTypeBase extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('WikiPageType', new WikiTypeBaseRegistry($this));
 		
		$this->addAttributeGroup('PageReferenceName', 'system');
 	}
 	
 	function createIterator() 
 	{
 		return new WikiTypeBaseIterator( $this );
 	}
 	
 	function getReferenceName()
 	{
 		return '';
 	}

	function getDefaultAttributeValue( $attr )
	{
 		switch ( $attr )
 		{
 			case 'PageReferenceName':
 				return strtolower($this->getReferenceName());
 				
 			case 'WikiEditor':
 				return getSession()->getProjectIt()->get('WikiEditorClass');
 			
 			default:
 				return parent::getDefaultAttributeValue( $attr ); 
 		}
	}
	
	function getPageNameObject( $object_id = '' )
	{
		return '?entity='.get_class($this);
	}
	
	function IsDeletedCascade( $object )
	{
		return false;
	}
}