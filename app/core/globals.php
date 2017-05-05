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
    return CacheEngineFS::Instance();
}