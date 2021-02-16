<?php
include_once SERVER_ROOT_PATH . "pm/classes/participants/ParticipantModelBuilder.php";
include "FieldRestMySettings.php";
include "FieldFormButtons.php";
include "FieldListOfProjectRoles.php";

class ProfileForm extends SettingsFormBase
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

        if ( getSession()->getProjectIt()->IsPortfolio() ) {
            $object->addAttribute('ModuleSettings', '', text(1906), true, false, text(2187));
        }
        else {
            if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED )
            {
                if ( $this->getObjectIt()->getId() > 0 && $this->getObjectIt()->getId() != GUEST_UID ) {
                    $object->setAttributeVisible('NotificationEmailType', true);
                    $object->setAttributeVisible('NotificationTrackingType', true);
                    $object->addAttribute('Buttons', '', '', true, false, '', 300);
                }

                $moduleIt = getFactory()->getObject('Module')->getExact('permissions/participants');
                $object->addAttribute('ProjectRoles', 'VARCHAR', text(2452), true, false,
                    str_replace('%1', $moduleIt->getUrl(),
                        str_replace('%2', $moduleIt->getDisplayName(), text(2453))
                    )
                );
                $object->setAttributeEditable('ProjectRoles', false);
            }
            $object->addAttribute('ModuleSettings', '', text(1906), true, false, text(1910));
        }
    }

 	function getPageTitle()
 	{
 	    return text(2619);
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

    function getBodyTemplate() {
        return "core/PageFormBody.php";
    }
}