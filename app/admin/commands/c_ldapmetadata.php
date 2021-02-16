<?php

class LDAPMetadata extends CommandForm
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

		$this->checkRequired( array('LDAPServer', 'DirectoryType', 'UserName', 'Password') );

 		return true;
 	}
 	
 	function create()
	{
		if ( $_REQUEST['Password'] != SHADOW_PASS ) {
			$ldap = new LDAP();
			$ldap->setServer($_REQUEST['LDAPServer']);
			$ldap->setUserName($_REQUEST['UserName']);
			$ldap->setPassword($_REQUEST['Password']);

			if ( !$ldap->connect() ) {
				$this->replyError( str_replace('%1', $ldap->getServer(), text(2764)) );
			}
		}

		$settings_path = DOCUMENT_ROOT.'conf/plugins/ee/settings.php';

		$file = fopen($settings_path, 'r', 1);
		
		if ( $file === false ) 
		{
			$this->replyError( str_replace('%1', $settings_path, text(2768)) );
		}

		$file_content = fread($file, filesize($settings_path));
		
		fclose($file);
		
		$file = fopen($settings_path, 'w', 1);
		
		if ( $file === false ) 
		{
			$this->replyError( str_replace('%1', $settings_path, text(2768)) );
		}

		$file_content = SettingsFile::setSettingValue('LDAP_TYPE', $_REQUEST['DirectoryType'], $file_content);

		fwrite( $file, $file_content );
		fclose( $file );
		
		$settings_path = DOCUMENT_ROOT.'conf/plugins/ee/settings_ldap_'.$_REQUEST['DirectoryType'].'.php';

		$file = fopen($settings_path, 'r', 1);
		
		if ( $file === false ) 
		{
			$this->replyError( str_replace('%1', $settings_path, text(2768)) );
		}

		$file_content = fread($file, filesize($settings_path));
		
		fclose($file);
		
		$file = fopen($settings_path, 'w', 1);
		
		if ( $file === false ) 
		{
			$this->replyError( str_replace('%1', $settings_path, text(2768)) );
		}

		$file_content = SettingsFile::setSettingValue('LDAP_SERVER', $_REQUEST['LDAPServer'], $file_content);
		$file_content = SettingsFile::setSettingValue('LDAP_USERNAME', $_REQUEST['UserName'], $file_content);
        if ( $_REQUEST['Password'] != SHADOW_PASS ) {
            $file_content = SettingsFile::setSettingValue('LDAP_PASSWORD', $_REQUEST['Password'], $file_content);
        }

		// build domain using server name
		if ( $_REQUEST['SearchDomain'] == '' )
		{
			$parts = preg_split('/:/', $_REQUEST['LDAPServer']);
			$parts = preg_split('/\./', $parts[0]);

			foreach( $parts as $key => $part) 
			{ 
				$parts[$key] = 'dc='.$part;
			}
			
			$_REQUEST['SearchDomain'] = join(',',$parts);
		}

		// convert domain from company.com notation to LDAP-syntax dc=company,dc=com
		$parts = preg_split('/\./', $_REQUEST['SearchDomain']);
		
		if ( count($parts) > 1 )
		{
		    foreach( $parts as $key => $part)
		    {
		        $parts[$key] = 'dc='.$part;
		    }
		    	
		    $_REQUEST['SearchDomain'] = join(',',$parts);
		}

		$file_content = SettingsFile::setSettingValue('LDAP_DOMAIN', $_REQUEST['SearchDomain'], $file_content);

		fwrite( $file, $file_content );
		fclose( $file );
		
		if ( function_exists('opcache_reset') ) opcache_reset();
		
		$this->replyRedirect( '/admin/ldap/?mode=metadata' );
	}
}
 