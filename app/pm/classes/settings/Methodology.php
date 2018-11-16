<?php

include "MethodologyIterator.php";

class Methodology extends Metaobject
{
 	function __construct() 
 	{
		parent::Metaobject('pm_Methodology');

		$this->removeAttribute('IsBlogUsed');
		$this->setAttributeDescription('IsKnowledgeUsed', text(678));
	}
	
	function createIterator() 
	{
		return new MethodologyIterator( $this );
	}
	
 	function IsDeletedCascade( $object )
	{
		return false;
	}
	
	function getPage()
	{
	    $session = getSession();
	    
		return $session->getApplicationUrl().'project/methodology?';
	}

	function getDefaultAttributeValue($name)
	{
		switch( $name ) {
			case 'IsRequirements':
				return 'N';
			default:
				return parent::getDefaultAttributeValue($name);
		}
	}
}