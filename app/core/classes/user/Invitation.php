<?php

include "InvitationIterator.php";

class Invitation extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
		parent::__construct('pm_Invitation', $registry);
		
		foreach( $this->getAttributes() as $attribute => $data )
		{
			$this->setAttributeVisible($attribute, false);
			$this->setAttributeRequired($attribute, false);
		}
		
		$this->setAttributeVisible('Addressee', true);
		$this->setAttributeRequired('Addressee', true);
		$this->setAttributeType('Addressee', 'VARCHAR');
		$this->setAttributeCaption('Addressee', 'Email');
		$this->setAttributeDescription('Addressee', text(1862));
	}
	
	function createIterator() 
	{
		return new InvitationIterator($this);
	}
	
	function getPageName()
	{
		return defined('PERMISSIONS_ENABLED') && getSession()->getProjectIt()->getId() != '' && !getSession()->getProjectIt()->IsPortfolio()
			? getSession()->getApplicationUrl($this).'invite'
			: '/invite';
	}
}