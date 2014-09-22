<?php

namespace Devprom\Component\HttpKernel\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

class DevpromBundle extends Bundle
{
    public function boot()
    {
        global $plugins, $session, $model_factory;
        
        $plugins = $this->getPluginsFactory();
         
		$model_factory = $this->getModelFactory();
         
        $session = $this->buildSession();
    }
	
	protected function buildSession()
	{
		return new \SessionBase(null, null, null, $this->getCacheService());		
	}
	
	protected function buildPluginsFactory()
	{
		return new \PluginsFactory();
	}
	
	protected function buildAccessPolicy()
	{
		return new \AccessPolicy($this->getCacheService());
	}
	
	protected function buildCacheService()
	{
		return new \CacheEngineFS();
	}
	
	protected function buildModelFactory()
	{
		return new \ModelFactoryExtended(
				$this->getPluginsFactory(), $this->getCacheService(), $this->buildAccessPolicy()
			);
	}
	
	protected function getPluginsFactory()
	{
		if ( is_object($this->plugins_factory) ) return $this->plugins_factory;
		
		$this->plugins_factory = $this->buildPluginsFactory();
		
		return $this->plugins_factory;
	}

	protected function getModelFactory()
	{
		if ( is_object($this->model_factory) ) return $this->model_factory;
		
		$this->model_factory = $this->buildModelFactory();
		
		return $this->model_factory;
	}
	
	protected function getCacheService()
	{
		if ( is_object($this->cache_service) ) return $this->cache_service;
		
		$this->cache_service = $this->buildCacheService();
		
		return $this->cache_service;
	}

	private $plugins_factory = null;
	
	private $model_factory = null;
	
	private $cache_service = null;
}