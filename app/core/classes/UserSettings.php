<?php

class UserSettings extends MetaobjectCacheable
{
 	function __construct( $entity_ref_name = 'cms_UserSettings' ) {
 		parent::__construct($entity_ref_name);
 	}
 	
 	function getSettingsValue( $settings_name, $user_id = 0 )
 	{
 		if ( $user_id == 0 ) $user_id = getSession()->getUserIt()->getId();
        return $this->getByRefArray(
                array(
                    'User' => $user_id,
                    'Settings' => $settings_name
                )
            )->get('Value');
 	}
 	
 	function setSettingsValue( $settings_name, $value, $user_id = 0 ) 
 	{
 		$parms = array();
 		
 		if ( $user_id == 0 ) $user_id = getSession()->getUserIt()->getId();

        $settingIt = $this->getByRefArray(
            array(
                'User' => $user_id,
                'Settings' => $settings_name
            )
        );

		if ( $settingIt->getId() != '' )
		{
			$parms['Value'] = $value;
			$this->modify_parms($settingIt->getId(), $parms);
		}
		else
		{
			$parms['User'] = $user_id;
			$parms['Settings'] = $settings_name;
			$parms['Value'] = $value;
			
			$this->add_parms($parms);
		}
 	}
}
