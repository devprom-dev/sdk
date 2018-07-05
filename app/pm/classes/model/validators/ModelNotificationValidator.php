<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelNotificationValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
        foreach( array('Notification', 'TransitionNotification') as $field ) {
            $notificationSpecified = array_key_exists($field, $parms) || array_key_exists($field.'OnForm', $parms);
            if ( $notificationSpecified && in_array($parms[$field], array('N','')) ) {
                $parms['IsPrivate'] = 'Y';
            }
        }
		return "";
	}
}