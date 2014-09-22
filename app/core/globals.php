<?php

function getConfiguration() 
{
	if ( class_exists(CONFIGURATION, false) )
	{
		$class_name = CONFIGURATION;
		return new $class_name;
	}
	else
	{
		return new CommunityConfiguration;
	} 
}
	
function _getServerUrl() 
{
 	return EnvironmentSettings::getServerUrl();
}

function getCacheService()
{
	if ( defined('CACHE_ENGINE') && class_exists(CACHE_ENGINE, false) )
	{
		$engine_name = CACHE_ENGINE;
		return new $engine_name;
	}
	else
	{
		return new CacheEngineFS();
	}
}