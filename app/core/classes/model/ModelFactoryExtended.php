<?php

class ModelFactoryExtended extends ModelFactory
{
    public function __construct( $pluginsManager = null, $cache_engine = null, $cache_key = 'global', $access_policy = null, $events_manager = null, $origination_service = null )
    {
        $cache_engine = !is_object($cache_engine) ? getCacheService() : $cache_engine;
        parent::__construct( $pluginsManager, $cache_engine, $cache_key, $access_policy, $events_manager, $origination_service );
    }

    protected function buildClasses()
    {
        if ( !is_object($this->getPluginsManager()) ) return parent::buildClasses();
        return array_merge( parent::buildClasses(), $this->getPluginsManager()->getClasses() );
    }
}