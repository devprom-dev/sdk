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
			$file_content = $this->updateContent( 
				'SERVER_NAME', $parms['ServerName'], $file_content );
		}

		if ( array_key_exists('ServerPort', $parms) )
		{ 
			$file_content = $this->updateContent( 
				'SERVER_PORT', $parms['ServerPort'], $file_content );
		}
		
		if ( array_key_exists('EmailTransport', $parms) )
		{ 
			$file_content = $this->updateContent( 
				'EMAIL_TRANSPORT', $parms['EmailTransport'], $file_content );
		}
		
		if ( array_key_exists('EmailSender', $parms) )
		{ 
			$file_content = $this->updateContent( 
				'EMAIL_SENDER_TYPE', $parms['EmailSender'], $file_content );
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

 		$value = defined('EMAIL_TRANSPORT') ? EMAIL_TRANSPORT : '1';
 		array_push( $columns, "'".$value."' EmailTransport " );
 		
 		$value = defined('EMAIL_SENDER_TYPE') ? EMAIL_SENDER_TYPE : 'user';
 		array_push( $columns, "'".$value."' EmailSender " );
 		
 		return $columns;
 	}

  	function updateContent( $parm, $value, $file_content )
 	{
		$regexp = "/(define\(\'".$parm."\'\,\s*\'[^']*\'\);)/mi";
		
		if ( preg_match( $regexp, $file_content, $match ) > 0 )
		{
			$file_content = preg_replace( $regexp,
				"define('".$parm."', '".$value."');", $file_content);
		}
		else
		{
		    if ( strpos($file_content, "?>") !== false )
		    {
    			$file_content = preg_replace( "/(\?>)/mi",
    				"\n\tdefine('".$parm."', '".$value."');\n?>", $file_content);
		    }
		    else
		    {
		        $file_content .= "\n\tdefine('".$parm."', '".$value."');\n";
		    }
		}
		
		return $file_content;
 	}
}