<?php

include_once "CacheEngine.php";

class CacheEngineVar extends CacheEngine
{
	function get( $key, $category )
	{
 		global $pm_session_cache;
 		
 		if ( is_array($pm_session_cache[$category]) ) return $pm_session_cache[$category][$key];
 		
 		return '';
	}
	
	function set( $key, $value, $category )
	{
 		global $pm_session_cache;
 		
 		if ( !is_array($pm_session_cache[$category]) ) $pm_session_cache[$category] = array();
 		
 		$pm_session_cache[$category][$key] = $value;
	}
	
	function reset( $key, $category )
	{
		unset($pm_session_cache[$category][$key]);
	}
	
	function truncate( $category )
	{
		unset($pm_session_cache[$category]);
	}
	
	function drop()
	{
		unset($pm_session_cache);
	}
}
