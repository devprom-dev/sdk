<?php
 
class ProfileNotificationsForm extends AjaxForm
{
    function extendModel()
    {
        parent::extendModel();

        $visible = $this->getObject()->getAttributesByGroup('notifications-tab');
        foreach( $this->getObject()->getAttributes() as $attribute => $data ) {
            $this->getObject()->setAttributeVisible($attribute, in_array($attribute, $visible));
        }
    }

    function getModifyCaption() {
 	    return text(1912);
 	}

 	function getCommandClass() {
 		return 'profilemanage';
 	}
 	
 	function getRedirectUrl() {
        return '/notifications';
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
            case 'NotificationEmailType':
                return text(2469);
            case 'NotificationTrackingType':
                return text(2468);
            case 'SendDeadlinesReport':
                return sprintf(text(3309),
                    defined('DEADLINE_REPORT_DUE_DAYS') ? DEADLINE_REPORT_DUE_DAYS : 4);
 		}
 	}
}
