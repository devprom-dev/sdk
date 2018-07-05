<?php

include_once SERVER_ROOT_PATH.'ext/idna/idna_convert.class.php';

class EnvironmentSettings
{
    static public function getCustomServerName()
    {
        return defined('SERVER_NAME') ? SERVER_NAME : ''; 
    }
    
    static public function getCustomServerPort() 
    {
        return defined('SERVER_PORT') ? SERVER_PORT : ''; 
    }

    static public function getProxyServer()
    {
        return defined('PROXY_SERVER') ? PROXY_SERVER : '';
    }

    static public function getProxyAuth()
    {
        return defined('PROXY_AUTH') ? PROXY_AUTH : '';
    }

    static public function getAutoUpdate()
    {
        return defined('AUTO_UPDATE') ? AUTO_UPDATE == 'Y' : false;
    }

    static public function getHttps()
    {
        return strtolower(getenv('HTTPS')) == 'on'; 
    }
    
    static public function getServerNameDefault()
    {
        $host = getenv('SERVER_NAME');

        if ( $host != '' ) return $host;
        
        if ( function_exists('apache_getenv') )
        {
            $host = apache_getenv('SERVER_NAME');

            if ( $host != '' ) return $host;
        }
        
        $host = gethostname(); 

        if ( $host != '' ) return $host;
        
        return 'localhost'; 
    }
    
    static public function getServerName()
    {
        $server_name = static::getCustomServerName();
        
        if ( $server_name != '' ) return $server_name;
        
        $server_name = self::getServerNameDefault();
        
		if ( class_exists('idna_convert', false) )
		{
 			$converter = new idna_convert();
 			$server_name = $converter->encode(IteratorBase::wintoutf8($server_name));
		}
		
		return $server_name;
    }
    
    static public function getServerAddress()
    {
        $host = getenv('SERVER_ADDR');

        if ( $host != '' ) return $host;
        
        if ( function_exists('apache_getenv') )
        {
            $host = apache_getenv('SERVER_ADDR');

            if ( $host != '' ) return $host;
        }
            
        return '127.0.0.1'; 
    }
    
    static public function getServerPort()
    {
        $port = static::getCustomServerPort();
        
        if ( $port != '' ) return $port;
        
        return self::getServerPortDefault();
    }
    
    static public function getServerPortDefault()
    {
    	$port = getenv('SERVER_PORT');

        if ( $port != '' ) return $port;
        
        if ( function_exists('apache_getenv') )
        {
            $port = apache_getenv('SERVER_PORT');

            if ( $port != '' ) return $port;
        }

        return '80'; 
    }
    
    static public function getServerSchema()
    {
        if ( static::getHttps() ) return 'https';
        
        if ( static::getServerPort() == '443' ) return 'https';
        
        return 'http';
    }
    
    static public function getServerUrl()
    {
        $schema = static::getServerSchema();
        return self::appendPort($schema, $schema.'://'.static::getServerName());
    }

    static public function getServerUrlByIpAddress()
    {
        $schema = static::getServerSchema();
        return self::appendPort($schema, $schema.'://'.gethostbyname(static::getServerName()));
    }

    static public function getServerUrlLocalhost()
    {
        $schema = static::getServerSchema();
        return self::appendPort($schema, $schema.'://127.0.0.1');
    }

    static protected function appendPort( $schema, $url )
    {
        $port = static::getServerPort();
        if ( $schema == 'https' && $port == 443 ) return $url;
        if ( $schema == 'http' && $port == 80 ) return $url;
        return $url.':'.$port;
    }

    static public function getUTCOffset()
    {
    	return defined('DEFAULT_UTC_OFFSET') ? DEFAULT_UTC_OFFSET : '+00';
    }

    static public function getPasswordLength()
    {
        return defined('PASSWORD_LENGTH') ? PASSWORD_LENGTH : '1';
    }

    static public function getClientTimeZone()
    {
    	return $_COOKIE['devprom-client-tz'] != '' 
    			? new DateTimeZone($_COOKIE['devprom-client-tz']) 
    			: new DateTimeZone('UTC');
    }
    
    static public function setClientTimeZone( $tz_name )
    {
    	if ( $_COOKIE['devprom-client-tz'] != '' ) return;
    	
    	$_COOKIE['devprom-client-tz'] = $tz_name;
    }
    
    static public function getClientTimeZoneUTC()
    {
    	if ( $_COOKIE['devprom-client-tz'] == '' ) return self::getUTCOffset().':00';
    	
    	$tz = self::getClientTimeZone();

    	$offset = $tz->getOffset(new DateTime("now"));

		$offsetHours = round(abs($offset)/3600); 
		$offsetMinutes = round((abs($offset) - $offsetHours * 3600) / 60);
	     
	    return ($offset < 0 ? '-' : '+').str_pad($offsetHours, 2, "0", STR_PAD_LEFT).":".str_pad($offsetMinutes, 2, "0", STR_PAD_LEFT);
    }
    
    static public function getWindows()
    {
        return strpos($_SERVER['OS'], 'Windows') !== false || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '';
    }
    
    static public function getBrowserPostUnicode()
    {
	    $browser_ie8_ie9 = strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8') > 0 || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9') > 0;
	    
	    return preg_match('/utf-8/i', $_SERVER['CONTENT_TYPE'])
	        || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !$browser_ie8_ie9; 
    }

    static public function getBrowserIE()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) and (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') || strpos($_SERVER['HTTP_USER_AGENT'],'Trident/'));
    }

    static public function getDownloadHeader( $filename )
    {
        if ( self::getBrowserIE() ) $filename = rawurlencode($filename);
        return 'Content-Disposition: attachment; filename="'.$filename.'"';
    }

    static public function getServerSalt()
    {
    	return md5(INSTALLATION_UID . PID_HASH);
    }
    
    static public function ajaxRequest()
    {
    	$headers = apache_request_headers();
    	return strtolower($headers['X-Requested-With']) == 'xmlhttprequest';
    }
    
    static public function getMaxFileSize()
    {
 		return defined('MAX_FILE_SIZE') ? MAX_FILE_SIZE : 104857600;
    }

    static public function getRawPostData() {
        return file_get_contents("php://input");
    }
}