<?php

include_once "CacheEngine.php";

class CacheEngineVar extends CacheEngine
{
	function get( $key, $path = '' )
	{
 		global $pm_session_cache;
 		
 		if ( is_array($pm_session_cache[$path]) ) return $pm_session_cache[$path][$key];
 		
 		return '';
	}
	
	function set( $key, $value, $path = '' )
	{
 		global $pm_session_cache;
 		
 		if ( !is_array($pm_session_cache[$path]) ) $pm_session_cache[$path] = array();
 		
 		$pm_session_cache[$path][$key] = $value;
	}
	
	function reset( $key, $path = '' )
	{
		global $pm_session_cache;

		unset($pm_session_cache[$path][$key]);
	}
	
	function invalidate( $path = '' )
	{
		unset($pm_session_cache);
	}
}
