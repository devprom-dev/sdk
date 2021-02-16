<?php
include "MailerSettingsFileSwiftMailer.php";

class MailerSettingsPersister extends ObjectSQLPersister
{
	private $fields = array (
		'MailServer',
		'MailServerPort',
		'Pop3Server',
		'Pop3ServerPort',
        'Pop3ServerType',
		'MailServerEncryption',
		'MailServerUser',
		'MailServerPassword',
		'MailServerType'
	);
	private $defaults = array (
		'MailServer' => '127.0.0.1',
		'MailServerPort' => '25',
		'Pop3Server' => '',
		'Pop3ServerPort' => '143',
        'Pop3ServerType' => '2',
		'MailServerEncryption' => 'auto',
		'MailServerUser' => '',
		'MailServerPassword' => '',
		'MailServerType' => 'smtp'
	);
	private $files;
	
	public function __construct()
	{
		$this->files = array (
			new MailerSettingsFileSwiftMailer()
		);
	}
	
 	function modify( $object_id, $parms )
 	{
 		foreach( $this->files as $file ) {
 			foreach( $this->fields as $attribute ) {
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
 	}
 	
 	function getSelectColumns( $alias )
 	{
 		$values = array();
 		
 		foreach( $this->files as $file ) {
 			if ( !$file->exists() ) continue;
 			
 		 	foreach( $this->fields as $attribute ) {
	 			$values[$attribute] = $file->read( $attribute );
 			}
 			break;
 		}
 		
 		if ( count($values) < 1 ) {
 			$values = $this->defaults;
 		}
 		else {
	 		foreach( $values as $key => $value ) {
	 			if ( $value == '' ) $values[$key] = $this->defaults[$key];
	 		}
 		}
 		
 		$columns = array();
		foreach( $values as $attribute => $value ) {
			$columns[] = "'".addslashes(addslashes($value))."' ".$attribute;
		}

		return $columns;
 	}
}