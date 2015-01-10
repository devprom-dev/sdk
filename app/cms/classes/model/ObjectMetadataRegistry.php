<?php

class ObjectMetadataRegistry
{
	public function getMetadata( Metaobject & $object, $cache_category = '' )
	{
	    $key = $object->getEntityRefName().get_class($object);
		
	    if ( isset($this->metadata_cache[$key]) ) return $this->metadata_cache[$key];
	    
	    return $this->metadata_cache[$key] = $this->buildMetadata($object, $cache_category);
	}
	
	protected function buildMetadata( Metaobject & $object, $cache_category = '' )
	{ 
	    // check the cache
	    $session_key = 'entity-metadata-'.md5($object->getEntityRefName().get_class($object));
	    
		$metadata = getFactory()->getCacheService()->get( $session_key, $cache_category );

		if ( !is_object($metadata) )
		{
			getFactory()->debug( 'Missed metadata cache "'.get_class($object).'" ' );
			
	    	$metadata = new ObjectMetadata($object);
	    	
			$metadata->build();
			
			getFactory()->getCacheService()->set( $session_key, $metadata, $cache_category );
		}
		else
		{
			getFactory()->debug( 'Hit metadata cache "'.get_class($object).'" ' );
		}
		
		return $metadata;
	}
	
	private $metadata_cache = array();
}