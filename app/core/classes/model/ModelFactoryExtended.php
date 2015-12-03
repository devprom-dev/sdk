<?php

class ModelFactoryExtended extends ModelFactory
{
	private $plugins = null;
	
	public function __construct( $pluginsManager = null, $cache_engine = null, $access_policy = null, $events_manager = null, $origination_service = null )
	{
		global $plugins;

		$plugins = $pluginsManager;
		$this->plugins = $pluginsManager;
		
		parent::__construct(
				is_object($cache_engine) ? $cache_engine : getCacheService(),
				$access_policy, 
				$events_manager, 
				$origination_service
		);
	}
	
	protected function buildClasses()
	{
		if ( !is_object($this->plugins) ) return parent::buildClasses();
		return array_merge( parent::buildClasses(), $this->plugins->getClasses() );
	}

	public function getPluginsManager() {
		return $this->plugins;
	}
}