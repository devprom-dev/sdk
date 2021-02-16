<?php
include SERVER_ROOT_PATH . "core/classes/system/validators/ModelValidatorSystemSettingsTimezone.php";
include "ui/FieldRestCache.php";

class SystemSettingsForm extends PageForm
{
	function __construct()
	{
		$object = getFactory()->getObject('cms_SystemSettings');
		$object->resetCache();
		parent::__construct( $object );
	}

	function extendModel()
    {
        parent::extendModel();
        $this->getObject()->addAttribute('ResetCache', '', '', true, false, '', 50);
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
		switch ( $attr_name )
		{
			case 'OrderNum':
			case 'AdminEmail':
				return false;
			default:
				return parent::IsAttributeVisible( $attr_name );
		}
	}

    function getBodyTemplate() {
        return "core/PageFormBody.php";
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
		switch ( $attr_name )
		{
			case 'Parameters':
				return new FieldLargeText();

			case 'EmailTransport':
				return new FieldDictionary(getFactory()->getObject('co_MailTransport'));

            case 'ResetCache':
                return new FieldRestCache(getSession()->getApplicationUrl().'clear-cache.php');
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
				$field->setRows( 18 );
				$field->setValue( $this->debugParameters() );
				break;
				
			case 'ServerPort':
				$field->setDefault(text(466).EnvironmentSettings::getServerPortDefault());
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
	
    function getValidators() {
        return array_merge(
            parent::getValidators(),
            array(
                new ModelValidatorSystemSettingsTimezone()
            )
        );
    }

    function getPageTitle()
    {
        return translate('Приложение');
    }

    function IsNeedButtonDelete()
    {
        return false;
    }
}

