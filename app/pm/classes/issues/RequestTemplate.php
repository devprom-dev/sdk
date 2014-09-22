<?php

include_once SERVER_ROOT_PATH."core/classes/templates/ObjectTemplate.php";

class RequestTemplate extends ObjectTemplate
{
	public function getListName()
	{
		return 'template-'.$this->getTypeName();
	}
	
	public function getTypeName()
	{
		return 'request'; 
	}
	
	public function getAttributesTemplated()
	{
		$object = getFactory()->getObject($this->getTypeName());
		
		$attributes = array_diff(
				array_keys($object->getAttributes()), 
				$object->getAttributesByGroup('system'),
				array( 'State', 'Project', 'RecordCreated', 'RecordModified', 'Author', 'OrderNum' )
		);
		
		return $attributes;
	}
	
	function getDisplayName()
	{
		return text(1520);
	}
	
	function getClassName()
	{
		return get_class($this);
	}
	
	function getPage()
	{
		return getSession()->getApplicationUrl($this).
			'project/dicts/RequestTemplate?ListName='.$this->getListName().
				'&ObjectClass=Request&';
	}
}