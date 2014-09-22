<?php

class ObjectMetadataRegistry
{
	public function getMetadata( Metaobject & $object )
	{
	    $key = $object->getEntityRefName().get_class($object);
		
	    if ( isset($this->metadata_cache[$key]) ) return $this->metadata_cache[$key];
	    
	    return $this->metadata_cache[$key] = $this->buildMetadata($object);
	}
	
	public function buildMetadata( Metaobject & $object )
	{
	    // check the cache
	    $session_key = 'entity-metadata-'.md5($object->getEntityRefName().get_class($object));
	    
		$metadata = getFactory()->getCacheService()->get( $session_key, $object->getMetadataCacheName() );

		if ( !is_object($metadata) )
		{
			getFactory()->debug( 'Missed metadata cache "'.get_class($object).'" ' );
			
	    	$metadata = new ObjectMetadata($object);
	    	
			$metadata->build();
			
			getFactory()->getCacheService()->set( $session_key, $metadata, $object->getMetadataCacheName() );
		}
		else
		{
			getFactory()->debug( 'Hit metadata cache "'.get_class($object).'" ' );
		}
		
		return $metadata;
	}
	
	private $metadata_cache = array();
}