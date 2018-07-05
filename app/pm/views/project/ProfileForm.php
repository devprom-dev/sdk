<?php
include_once SERVER_ROOT_PATH . "pm/classes/participants/ParticipantModelBuilder.php";
include "FieldRestMySettings.php";
include "FieldFormButtons.php";
include "FieldListOfProjectRoles.php";

class ProfileForm extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        $builder = new ParticipantModelBuilder();
        $builder->build($object);

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
            $object->setAttributeVisible('NotificationEmailType', true);
            $object->setAttributeVisible('NotificationTrackingType', true);

            if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED )
            {
                $moduleIt = getFactory()->getObject('Module')->getExact('permissions/participants');
                $object->addAttribute('ProjectRoles', 'VARCHAR', text(2452), true, false,
                    str_replace('%1', $moduleIt->getUrl(),
                        str_replace('%2', $moduleIt->getDisplayName(), text(2453))
                    )
                );
                $object->setAttributeEditable('ProjectRoles', false);
            }
            $object->addAttribute('Buttons', '', '', true, false, '', 300);
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
            case 'NotificationEmailType':
            case 'NotificationTrackingType':
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

		    case 'NotificationEmailType':
		        $field = new FieldDictionary(getFactory()->getObject('Notification'));
                $field->setNullTitle(text(2451));
		    	return $field;

            case 'NotificationTrackingType':
                $field = new FieldDictionary(getFactory()->getObject('NotificationTrackingType'));
                $field->setNullOption(false);
                return $field;

            case 'ProjectRoles':
                return new FieldListOfProjectRoles(getSession()->getParticipantIt());
		    	
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