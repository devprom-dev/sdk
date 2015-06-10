<?php

include "FieldRestMySettings.php";
include "FieldFormButtons.php";

class ProfileForm extends PMPageForm
{
	function buildRelatedDataCache()
	{
		parent::buildRelatedDataCache();
		
		$object = $this->getObject();
		
		foreach( $object->getAttributes() as $attribute => $data )
		{
			$object->setAttributeVisible($attribute, false);
		}
		
		$object->setAttributeVisible('Notification', true);
		$object->setAttributeCaption('Notification', text(1912));
		$object->setAttributeDescription('Notification', text(1913));
		
		$object->addAttribute('Buttons', '', '', true, false, '');
		$object->addAttribute('ModuleSettings', '', text(1906), true, false, text(1910));
		$object->addAttribute('MenuSettings', '', text(1907), true, false, '<br/>'.text(1911));
	}
	
	function transformInputValues( $id, $action )
	{
		global $_REQUEST;
		
		if($_REQUEST['IsActive'] == '') $_REQUEST['IsActive'] = 'Y';
	}
 	
 	function getCaption() 
 	{
 	    return text(1292);
	}
	
	function getFormPage() {
		return 'profile';
	}
	
 	function IsNeedButtonNew() {
		return false;
	}
 	function IsNeedButtonCopy() {
		return false;
	}
 	function IsNeedButtonDelete() {
		return false;
	}
 	function IsNeedButtonSave() {
		return true;
	}
	
 	function IsAttributeEditable( $attr ) 
 	{
 		return true;
 	}
 	
 	function checkAccess()
 	{
 		return true;
 	}
 	
	function IsAttributeVisible( $attr ) 
 	{
 		switch ( $attr )
 		{
 			case 'IsActive':
 			case 'SystemUser':
 			    return false;

 			default:
 				return parent::IsAttributeVisible( $attr );
 		}
	}
	
	function getActions()
	{
	    return array();
	}

	function createFieldObject( $name ) 
	{
		switch( $name )
		{
		    case 'Buttons':
		    	return new FieldFormButtons($this);
			
			case 'ModuleSettings':
		    	return new FieldRestMySettings(getSession()->getApplicationUrl().'settings/modules');

		    case 'MenuSettings':
		    	return new FieldRestMySettings(getSession()->getApplicationUrl().'settings/menu');

		    case 'Notification':
		    	return new FieldDictionary(getFactory()->getObject('Notification'));
		    	
		    default:
		    	return parent::createFieldObject( $name );
		}
	}
	
	function drawButtonsOriginal()
	{
		parent::drawButtons();
	}
	
	function drawButtons()
	{
	}
}