<?php

include SERVER_ROOT_PATH."admin/views/ui/EmailSenderDictionary.php";

class SystemSettingsForm extends PageForm
{
	function __construct()
	{
		global $model_factory;

		$object = $model_factory->getObject('cms_SystemSettings');
		
		$object->addAttribute( 'Parameters', 'TEXT', text(1078), true );
		
		$object->resetCache();

		parent::__construct( $object );
	}

	function validateInputValues( $id, $action )
	{
	    $result = parent::validateInputValues( $id, $action );
	    
	    if ( $result != '' ) return $result;
	    
	    $parts = preg_split('/:/', $_REQUEST['ServerName']);
	    
	    if ( count($parts) > 1 )
	    {
	        return text(1429);
	    }
	}
	
	function IsAttributeVisible( $attr_name )
	{
		global $plugins;
			
		switch ( $attr_name )
		{
			case 'OrderNum':
			case 'AdminEmail':
				return false;
					
			case 'ServerName':
			case 'Parameters':
				return true;
					
			default:
				return parent::IsAttributeVisible( $attr_name );
		}
	}

	function getFieldDescription( $name )
	{
		switch ( $name )
		{
			case 'AllowToChangeLogin':
				return text(411);

			default:
				return parent::getFieldDescription( $name );
		}
	}

	function createFieldObject( $attr_name )
	{
		global $model_factory;

		switch ( $attr_name )
		{
			case 'EmailSender':
				return new EmailSenderDictionary();
				
			case 'Parameters':
				return new FieldLargeText();

			case 'EmailTransport':
				return new FieldDictionary($model_factory->getObject('co_MailTransport'));
		}

		return parent::createFieldObject( $attr_name );
	}

	function createField( $attr )
	{
		$field = parent::createField( $attr );
		if ( !is_object($field) ) return $field;

		switch ( $attr )
		{
			case 'Parameters':
				$field->setReadonly( true );
				$field->setRows( 18 );
				$field->setValue( $this->debugParameters() );
				break;
				
			case 'ServerPort':
				$field->setDefault(text(466).EnvironmentSettings::getServerPort());
				break;

			case 'ServerName':
				$field->setDefault(text(466).EnvironmentSettings::getServerName());
				break;
		}

		return $field;
	}

	function debugParameters()
	{
		$constants = array (
			'DB_HOST',
			'DB_USER',
			'DB_NAME',
			'SERVER_ROOT',
			'MYSQL_ENCRYPTION_ALGORITHM',
			'METRICS_VISIBLE',
			'ZIP_HELP_COMMAND',
			'ZIP_APPEND_COMMAND',
			'UNZIP_HELP_COMMAND',
			'UNZIP_COMMAND',
			'MYSQLDUMP_HELP_COMMAND',
			'MYSQL_HELP_COMMAND',
			'MYSQL_INSTALL_COMMAND',
			'MYSQL_BACKUP_COMMAND',
			'MYSQL_UPDATE_COMMAND',
			'MYSQL_APPLY_COMMAND',
			'SERVER_FILES_PATH',
			'SERVER_BACKUP_PATH',
			'SERVER_UPDATE_PATH',
			'SERVER_CORPDB_PATH',
			'SERVER_CORPMYSQL_PATH',
			'SERVER_ROOT_PATH',
			'DOCUMENT_ROOT',
			'CACHE_PATH'
			);
			asort($constants);

			$lines = array();
			foreach ( $constants as $key => $value )
			{
				if ( defined($value) ) array_push( $lines, $value.'='.constant($value) );
			}

			return join($lines, "\r\n");
	}
	
	function getCaption()
	{
	    
	}
}
