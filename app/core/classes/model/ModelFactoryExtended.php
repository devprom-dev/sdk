<?php

class ModelFactoryExtended extends ModelFactory
{
	public function __construct( $pluginsManager = null, $cache_engine = null, $access_policy = null, $events_manager = null, $origination_service = null )
	{
		parent::__construct(
			$pluginsManager,
			is_object($cache_engine) ? $cache_engine : getCacheService(),
			$access_policy,
			$events_manager,
			$origination_service
		);
	}
	
	protected function buildClasses()
	{
		if ( !is_object($this->getPluginsManager()) ) return parent::buildClasses();
		return array_merge( parent::buildClasses(), $this->getPluginsManager()->getClasses() );
	}
}