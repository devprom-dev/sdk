<?php

class ObjectMetadataRegistry
{
    private $metadata = array();
    const CACHE_PREFIX = 'em-';

	public function getMetadata( Metaobject & $object, $cache_category = '' )
	{
        $key = md5($object->getEntityRefName().get_class($object));
        if ( is_object($this->metadata[$cache_category][$key]) ) {
            return $this->metadata[$cache_category][$key];
        }
	    return $this->metadata[$cache_category][$key] = $this->buildMetadata($key, $object, $cache_category);
	}
	
	protected function buildMetadata( $key, Metaobject & $object, $cache_category = '' )
	{ 
	    // check the cache
	    $session_key = self::CACHE_PREFIX.$key;
		$metadata = getFactory()->getCacheService()->get( $session_key, $cache_category );

		if ( !is_object($metadata) ) {
            getFactory()->debug( 'Missed metadata cache "'.get_class($object).'" on '.$session_key.' at '.$cache_category );
	    	$metadata = new ObjectMetadata($object);
			$metadata->build();
            getFactory()->getCacheService()->set(self::CACHE_PREFIX . $key, $metadata, $cache_category);
		}
		else {
			getFactory()->debug( 'Hit metadata cache "'.get_class($object).'" ' );
		}
		
		return $metadata;
	}
}