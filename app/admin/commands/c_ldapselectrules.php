<?php

include DOCUMENT_ROOT.'conf/plugins/ee/settings.php';

class LDAPSelectRules extends CommandForm
{
 	function getLogger()
 	{
 		try
 		{
 			return Logger::getLogger('LDAP');
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 			
 			return null;
 		}
 	}
 	
 	function validate()
 	{
 		global $_REQUEST, $model_factory;

		$this->checkRequired( array('LoginAttribute', 'EmailAttribute', 'SearchAttributes') );

 		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory;
		
		$settings_path = LDAP_METADATA_FILEPATH;

		$file = fopen($settings_path, 'r', 1);
		
		if ( $file === false ) {
			$this->replyError( str_replace('%1', $settings_path, text(2768)) );
		}

		$file_content = fread($file, filesize($settings_path));
		
		fclose($file);
		
		$file = fopen($settings_path, 'w', 1);
		
		if ( $file === false ) 
		{
			$this->replyError( str_replace('%1', $settings_path, text(2768)) );
		}

		$file_content = SettingsFile::setSettingValue('LDAP_LOGIN_ATTR', $_REQUEST['LoginAttribute'], $file_content);
		$file_content = SettingsFile::setSettingValue('LDAP_ROOTQUERY', $_REQUEST['SearchAttributes'], $file_content);
		$file_content = SettingsFile::setSettingValue('LDAP_EMAIL_ATTR', $_REQUEST['EmailAttribute'], $file_content);

		fwrite( $file, $file_content );
		fclose( $file );
		
		if ( function_exists('opcache_reset') ) opcache_reset();
		
		$this->replyRedirect( '/admin/ldap/?mode=selectrules' );
	}
}
 