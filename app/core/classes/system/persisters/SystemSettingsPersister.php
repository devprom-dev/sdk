<?php

class SystemSettingsPersister extends ObjectSQLPersister
{
 	function modify( $object_id, $parms )
 	{
 	    global $_SERVER;

		$settings_path = DOCUMENT_ROOT.'settings_server.php';
		
		$file = fopen($settings_path, 'r', 1);

		$file_content = fread($file, filesize($settings_path));
		fclose($file);

		if ( array_key_exists('ServerName', $parms) )
		{ 
			$file_content = SettingsFile::setSettingValue(
				'SERVER_NAME', $parms['ServerName'], $file_content );
		}

		if ( array_key_exists('ServerPort', $parms) )
		{ 
			$file_content = SettingsFile::setSettingValue(
				'SERVER_PORT', $parms['ServerPort'], $file_content );
		}

        if ( array_key_exists('ProxyServer', $parms) ) {
            $file_content = SettingsFile::setSettingValue(
                'PROXY_SERVER', $parms['ProxyServer'], $file_content );
        }

        if ( array_key_exists('ProxyAuth', $parms) ) {
            $file_content = SettingsFile::setSettingValue(
                'PROXY_AUTH', $parms['ProxyAuth'], $file_content );
        }

        if ( array_key_exists('AutoUpdateOnForm', $parms) ) {
            $file_content = SettingsFile::setSettingValue(
                'AUTO_UPDATE', $parms['AutoUpdate'] == 'Y' ? 'Y' : 'N', $file_content );
        }

        if ( array_key_exists('EmailTransport', $parms) )
		{ 
			$file_content = SettingsFile::setSettingValue(
				'EMAIL_TRANSPORT', $parms['EmailTransport'], $file_content );
		}
		
 		if ( array_key_exists('TimeZoneUTC', $parms) )
		{ 
			$parts = preg_split('/:/', $parms['TimeZoneUTC']);
			$value = array_shift($parts);
			$file_content = SettingsFile::setSettingValue('DEFAULT_UTC_OFFSET', $value, $file_content);
		}

		if ( array_key_exists('PasswordLength', $parms) )
		{
			$parts = preg_split('/:/', $parms['PasswordLength']);
			$value = array_shift($parts);
			$file_content = SettingsFile::setSettingValue('PASSWORD_LENGTH', $value, $file_content);
		}

		$file = fopen($settings_path, 'w', 1);
		fwrite( $file, $file_content );
		fclose( $file );
		
		if ( function_exists('opcache_reset') ) opcache_reset();
 	}
 	
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, "'".EnvironmentSettings::getCustomServerName()."' ServerName " );
 		array_push( $columns, "'".EnvironmentSettings::getCustomServerPort()."' ServerPort " );
        array_push( $columns, "'".EnvironmentSettings::getProxyServer()."' ProxyServer " );
        array_push( $columns, "'".EnvironmentSettings::getProxyAuth()."' ProxyAuth " );
        array_push( $columns, "'".(EnvironmentSettings::getAutoUpdate() ? 'Y' : 'N')."' AutoUpdate " );
		array_push( $columns, "'".EnvironmentSettings::getUTCOffset()."' TimeZoneUTC " );
		array_push( $columns, "'".EnvironmentSettings::getPasswordLength()."' PasswordLength " );
        array_push( $columns, "'".EnvironmentSettings::getPlantUMLServer()."' PlantUMLServer " );
        array_push( $columns, "'".EnvironmentSettings::getMathJaxServer()."' MathJaxServer " );

 		return $columns;
 	}
}