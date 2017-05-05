<?php

include "FieldRestMySettings.php";
include "FieldFormButtons.php";

class ProfileForm extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();

        foreach( $object->getAttributes() as $attribute => $data ) {
            $object->setAttributeVisible($attribute, false);
            $object->setAttributeRequired($attribute, false);
        }

        $object->addAttribute('ApiKey', 'VARCHAR', text(2285), true, false, text(2286), 400);
        $object->setAttributeEditable('ApiKey', false);

        if ( getSession()->getProjectIt()->IsPortfolio() ) {
            $object->addAttribute('ModuleSettings', '', text(1906), true, false, text(2187));
        }
        else {
            $object->setAttributeVisible('Notification', true);
            $object->setAttributeCaption('Notification', text(1912));
            $object->setAttributeDescription('Notification', text(1913));
            $object->addAttribute('Buttons', '', '', true, false, '');
            $object->addAttribute('ModuleSettings', '', text(1906), true, false, text(1910));
        }
    }

 	function getPageTitle()
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

    function IsAttributeEditable( $attr )
    {
        switch( $attr ) {
            case 'Notification':
                return true;
            default:
                return parent::IsAttributeEditable( $attr );
        }
    }

	function getFieldValue($field)
    {
        switch( $field ) {
            case 'ApiKey':
                return \AuthenticationAPIKeyFactory::getAuthKey($this->getObjectIt()->getRef('SystemUser'));
            default:
                return parent::getFieldValue($field);
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