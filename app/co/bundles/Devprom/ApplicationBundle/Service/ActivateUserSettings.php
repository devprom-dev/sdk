<?php

class ActivateUserSettings extends PMUserSettings
{
 	var $user_id;
 	
 	function __construct ( $user_id )
 	{
 		$this->user_id = $user_id;
 		
 		parent::__construct();
 	}
}