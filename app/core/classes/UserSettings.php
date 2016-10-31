<?php

class UserSettings extends MetaobjectCacheable
{
 	var $cache;
 	
 	function __construct( $entity_ref_name = 'cms_UserSettings' ) 
 	{
 		parent::__construct($entity_ref_name);
 	}
 	
 	function getSettingsValue( $settings_name, $user_id = 0 )
 	{
 		if ( $user_id == 0 ) $user_id = getSession()->getUserIt()->getId();
 		
 		if ( !is_object($this->cache) )
 		{
	 		$this->cache = $this->getByRef('User', $user_id );
 		}

		$this->cache->moveTo('Settings', $settings_name);
		
		if ( !$this->cache->end() )
		{
			return $this->cache->get('Value');
		}
		else
		{
 			return '';
		}
 	}
 	
 	function setSettingsValue( $settings_name, $value, $user_id = 0 ) 
 	{
 		$parms = array();
 		
 		if ( $user_id == 0 ) $user_id = getSession()->getUserIt()->getId(); 
 		
 		if ( !is_object($this->cache) )
 		{
	 		$this->cache = $this->getByRef('User', $user_id);
 		}

		$this->cache->moveTo('Settings', $settings_name);

		if ( $this->cache->getId() != '' )
		{
			$parms['Value'] = $value;

			$this->modify_parms($this->cache->getId(), $parms);
		}
		else
		{
			$parms['User'] = $user_id;
			$parms['Settings'] = $settings_name;
			$parms['Value'] = $value;
			
			$this->add_parms($parms);
		}

		$this->cache = null;
 	}
}
