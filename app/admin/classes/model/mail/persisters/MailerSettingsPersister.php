<?php

include_once SERVER_ROOT_PATH.'admin/install/Installable.php';
include_once SERVER_ROOT_PATH.'admin/install/ClearCache.php';

include "MailerSettingsFileSendmail.php";
include "MailerSettingsFileSwiftMailer.php";

class MailerSettingsPersister extends ObjectSQLPersister
{
	private $attributes = array ( 'MailServer', 'MailServerPort', 'MailServerEncryption', 'MailServerUser', 'MailServerPassword', 'MailServerType' );
	
	private $defaults = array ( 'MailServer' => '', 'MailServerPort' => '25', 'MailServerEncryption' => 'auto', 'MailServerUser' => '', 'MailServerPassword' => '', 'MailServerType' => 'smtp' );
	
	private $files;
	
	public function __construct()
	{
		$this->files = array (
					new MailerSettingsFileSwiftMailer()
				);
		if ( EnvironmentSettings::getWindows() ) {
			$this->files[] = new MailerSettingsFileSendmail();
		}
	}
	
 	function modify( $object_id, $parms )
 	{
 		foreach( $this->files as $file )
 		{
 			foreach( $this->attributes as $attribute )
 			{
	 			if ( !array_key_exists($attribute, $parms) ) continue;
	 			
	 			$file->write( $attribute, $parms[$attribute] );
 			}
 		}

 		$settings = getFactory()->getObject('cms_SystemSettings');
 		
 		$settings->modify_parms(
 				$settings->getAll()->getId(), 
	 			array (
	 				'AdminEmail' => $parms['AdminEmail']
	 			) 
 		);
 		
		$command = new ClearCache();
		
		$command->install();
 		
		$checkpoint = getCheckpointFactory()->getCheckpoint( 'CheckpointSystem' );

	    $checkpoint->checkOnly( array('CheckpointWindowsSMTP') );
 	}
 	
 	function getSelectColumns( $alias )
 	{
 		$values = array();
 		
 		foreach( $this->files as $file )
 		{
 			if ( !$file->exists() ) continue;
 			
 		 	foreach( $this->attributes as $attribute )
 			{
	 			$values[$attribute] = $file->read( $attribute );
 			}
 			
 			break;
 		}
 		
 		if ( count($values) < 1 )
 		{
 			$values = $this->defaults;
 		}
 		else
 		{
	 		foreach( $values as $key => $value )
	 		{
	 			if ( $value == '' ) $values[$key] = $this->defaults[$key];
	 		}
 		}
 		
 		$columns = array();

		foreach( $values as $attribute => $value )
		{
			$columns[] = "'".$value."' ".$attribute;
		}

		return $columns;
 	}
}