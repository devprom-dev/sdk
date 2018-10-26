<?php

class SanitizeUrl
{
	private static $applications = array('admin', 'api', 'cache', 'co', 'core', 'file', 'images', 'plugins', 'pm', 'tasks');
	
	static public function parseUrl( $url ) {
		return preg_replace("/[']/", '', strip_tags(html_entity_decode(urldecode($url))));
	}

	static public function parseScript( $script ) {
        return preg_replace("/['\"]/", '', $script);
    }

	static public function parseSystemUrl( $url )
	{ 
		$parts = parse_url(SanitizeUrl::parseUrl($url));
		
		if ( filter_var(EnvironmentSettings::getServerUrl().$parts['path'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false ) return "";
		
		$url = $parts['path'];
		
		$path_parts = preg_split('/\//', trim($url, ' /'));

		// allow redirects inside application only 
		if ( !in_array($path_parts[0], self::$applications) ) return "";
		
		// restore query string
		if ( $parts['query'] != '' ) $url .= '?'.$parts['query'];
		
		return $url;
	}
	
	static public function parseUrlSkipQueryString( $url )
	{
		$parts = parse_url(SanitizeUrl::parseSystemUrl($url));
		
		return EnvironmentSettings::getServerUrl().$parts['path'];
	}
}