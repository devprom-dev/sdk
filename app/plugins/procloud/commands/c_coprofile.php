<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_advisemanage.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 require_once(dirname(__FILE__).'/../../../co/commands/c_profilemanage.php');
 
 class CoProfile extends ProfileManage
 {
 	function validate()
 	{
 		return parent::validate();
 	}
 	
 	function modify( $user_id )
	{
		return parent::modify( $user_id );
	}
 
 	function getResultDescription( $result )
	{
		return parent::getResultDescription( $result );
	}
 }
 
?>